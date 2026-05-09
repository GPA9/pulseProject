<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Concert;
use App\Models\Merch;

class CartController extends Controller
{
    // ── Ver el carrito ────────────────────────────────────────────────
    public function index()
    {
        $cart = session()->get('cart', []);
        return view('cart.index', compact('cart'));
    }

    // ── Añadir al carrito ─────────────────────────────────────────────
    public function add(Request $request)
    {
        $request->validate([
            'item_type' => 'required|in:concert,merch',
            'item_id'   => 'required|integer',
            'quantity'  => 'required|integer|min:1|max:20',
            'size'      => 'nullable|string|max:10',
        ]);

        $type = $request->item_type;
        $id   = (int) $request->item_id;
        $qty  = (int) $request->quantity;
        $size = $type === 'merch' ? trim($request->size ?? '') : '';

        // Load item to get name and price
        $item = match ($type) {
            'concert' => Concert::with('musicianProfile')->findOrFail($id),
            'merch'   => Merch::with('musicianProfile')->findOrFail($id),
        };

        $name  = match ($type) {
            'concert' => 'Entrada: ' . ($item->musicianProfile->stage_name ?? '') . ' en ' . $item->venue,
            'merch'   => $item->name . ' — ' . ($item->musicianProfile->stage_name ?? ''),
        };
        // Append size to name when applicable
        if ($size !== '') {
            $name .= ' (Talla: ' . $size . ')';
        }
        $price = (float) $item->price;

        // Check concert capacity
        if ($type === 'concert' && $item->capacity_available !== null) {
            $currentInCart = 0;
            foreach (session()->get('cart', []) as $ci) {
                if ($ci['type'] === 'concert' && (int)$ci['id'] === $id) {
                    $currentInCart = $ci['quantity'];
                }
            }
            if (($currentInCart + $qty) > $item->capacity_available) {
                return back()->with('cart_error', "Solo quedan {$item->capacity_available} entradas disponibles.");
            }
        }

        $cart = session()->get('cart', []);

        // Deduplicate by type + id + size (different sizes = different lines)
        $found = false;
        foreach ($cart as &$ci) {
            if ($ci['type'] === $type && (int)$ci['id'] === $id && ($ci['size'] ?? '') === $size) {
                $ci['quantity'] += $qty;
                $found = true;
                break;
            }
        }
        unset($ci);

        if (!$found) {
            $cart[] = [
                'type'     => $type,
                'id'       => $id,
                'name'     => $name,
                'price'    => $price,
                'quantity' => $qty,
                'size'     => $size,
                'image'    => ($type === 'merch' && $item->image_path) ? $item->image_path : null,
            ];
        }

        session()->put('cart', $cart);

        return back()->with('cart_success', '¡Añadido al carrito!');
    }

    // ── Actualizar cantidad en carrito ────────────────────────────────
    public function update(Request $request)
    {
        $request->validate([
            'index'    => 'required|integer|min:0',
            'quantity' => 'required|integer|min:1|max:20',
        ]);

        $cart = session()->get('cart', []);
        $idx  = (int) $request->index;

        if (isset($cart[$idx])) {
            $cart[$idx]['quantity'] = (int) $request->quantity;
            session()->put('cart', $cart);
        }

        return back();
    }

    // ── Eliminar del carrito ──────────────────────────────────────────
    public function remove(Request $request)
    {
        $request->validate(['index' => 'required|integer|min:0']);

        $cart = session()->get('cart', []);
        $idx  = (int) $request->index;

        if (isset($cart[$idx])) {
            array_splice($cart, $idx, 1);
            session()->put('cart', $cart);
        }

        return back()->with('cart_success', 'Producto eliminado del carrito.');
    }

    // ── Vaciar carrito ────────────────────────────────────────────────
    public function clear()
    {
        session()->forget('cart');
        return back()->with('cart_success', 'Carrito vaciado.');
    }
}
