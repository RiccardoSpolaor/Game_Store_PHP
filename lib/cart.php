<?php

if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    die("Non autorizzato");
}

class Cart {
    protected $cart_content = array();
    protected $cart_total = array();

    public function __construct()
    {
        $this->cart_content = array ();
        $this->cart_total = array('total_price' => 0, 'total_items' => 0);
    }

    public function getCartContent()
    {
        return $this->cart_content;
    }

    public function getTotalItems () {
        return $this -> cart_total['total_items'];
    }

    public function getTotalPrice () {
        return $this->cart_total['total_price'];
    }

    public function addItem ($item, $amount) {
        if (isset($this->cart_content[$item['id']])) {
            $this->cart_content[$item['id']]['quantity'] += $amount;
        }
        else
            $this->cart_content[$item['id']] = array (
                'title' => $item['title'],
                'price' => floatval ($item['price']),
                'console' => $item['console'],
                'quantity' => $amount
            );
        $this->cart_total['total_items'] += $amount;
        $this->cart_total['total_price'] += $item['price'] * $amount;
    }

    public function removeItem ($id, $amount) {
        if (isset($this->cart_content[$id])) {
            if ($amount == $this->cart_content[$id]['quantity']) {
                $this->cart_total['total_price'] -= ($this->cart_content[$id]['price'] * $amount);
                unset($this->cart_content[$id]);
            }
            elseif ($amount < $this->cart_content[$id]['quantity']) {
                $this->cart_total['total_price'] -= ($this->cart_content[$id]['price'] * $amount);
                $this->cart_content[$id]['quantity'] -= $amount;
            }
            else {
                return;
            }
            $this->cart_total['total_items'] -= $amount;

        }
    }

    public function destroy () {
        $this->cart_content = $this->cart_content = array('total_price' => 0, 'total_items' => 0);
    }
}

