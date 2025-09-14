<?php

class order extends post
{
    protected $sum, $user_shipping_price,$coupon_price, $user_id = 0;
    protected $user_name, $user_phone, $user_providence, $coupon, $user_address, $user_notes, $user_postal_code, $payment, $shipping, $days = "";
    protected $items = [];
    function __construct($post_identifier = "")
    {
        parent::__construct($post_identifier);
        $this->set_post_type('shop_order');
    }



    public function get_sum()
    {
        if (!$this->sum || strlen($this->sum) == 0) {
            $this->sum = $this->get_meta('sum');
        }
        if (!$this->sum || strlen($this->sum) == 0) {
            $this->sum = $this->get_meta('_order_total');
        }
        return $this->sum;
    }
    public function set_sum($new_sum)
    {
        base::RunQuery("DELETE FROM `post_meta` WHERE `key` = 'sum' AND `post_id` = {$this->post_id}");
        if (strlen($new_sum) > 0) {
            $this->sum = $new_sum;
            base::RunQuery("INSERT INTO `post_meta`(`post_id`,`key`,`value`) VALUES ({$this->post_id},'sum',{$this->sum})");
        }
    }



    public function get_user_shipping_price()
    {
        if (!$this->user_shipping_price || strlen($this->user_shipping_price) == 0) {
            $this->user_shipping_price = $this->get_meta('user_shipping_price');
        }
        if (!$this->user_shipping_price || strlen($this->user_shipping_price) == 0) {
            $this->user_shipping_price = $this->get_meta('_order_shipping');
        }
        return $this->user_shipping_price;
    }
    public function set_user_shipping_price($new_user_shipping_price)
    {
        base::RunQuery("DELETE FROM `post_meta` WHERE `key` = 'user_shipping_price' AND `post_id` = {$this->post_id}");
        if (strlen($new_user_shipping_price) > 0) {
            $this->user_shipping_price = $new_user_shipping_price;
            base::RunQuery("INSERT INTO `post_meta`(`post_id`,`key`,`value`) VALUES ({$this->post_id},'user_shipping_price',{$this->user_shipping_price})");
        }
    }



    public function get_user_id()
    {
        if (!$this->user_id || strlen($this->user_id) == 0) {
            $this->user_id = $this->get_meta('user_id');
        }
        if (!$this->user_id || strlen($this->user_id) == 0) {
            $this->user_id = $this->get_meta('_customer_user');
        }
        return $this->user_id;
    }
    public function set_user_id($new_user_id)
    {
        base::RunQuery("DELETE FROM `post_meta` WHERE `key` = 'user_id' AND `post_id` = {$this->post_id}");
        if (strlen($new_user_id) > 0) {
            $this->user_id = $new_user_id;
            base::RunQuery("INSERT INTO `post_meta`(`post_id`,`key`,`value`) VALUES ({$this->post_id},'user_id',{$this->user_id})");
        }
    }



    public function get_user_name()
    {
        if (!$this->user_name || strlen($this->user_name) == 0) {
            $this->user_name = $this->get_meta('user_name');
        }
        if (!$this->user_name || strlen($this->user_name) == 0) {
            $this->user_name = $this->get_meta('_billing_first_name') . ' ' . $this->get_meta('_billing_last_name');
        }
        return $this->user_name;
    }
    public function set_user_name($new_user_name)
    {
        base::RunQuery("DELETE FROM `post_meta` WHERE `key` = 'user_name' AND `post_id` = {$this->post_id}");
        if (strlen($new_user_name) > 0) {
            $this->user_name = $new_user_name;
            base::RunQuery("INSERT INTO `post_meta`(`post_id`,`key`,`value`) VALUES ({$this->post_id},'user_name','{$this->user_name}')");
        }
    }



    public function get_user_phone()
    {
        if (!$this->user_phone || strlen($this->user_phone) == 0) {
            $this->user_phone = $this->get_meta('user_phone');
        }
        if (!$this->user_phone || strlen($this->user_phone) == 0) {
            $this->user_phone = $this->get_meta('_billing_phone');
        }
        return $this->user_phone;
    }
    public function set_user_phone($new_user_phone)
    {
        base::RunQuery("DELETE FROM `post_meta` WHERE `key` = 'user_phone' AND `post_id` = {$this->post_id}");
        if (strlen($new_user_phone) > 0) {
            $this->user_phone = $new_user_phone;
            base::RunQuery("INSERT INTO `post_meta`(`post_id`,`key`,`value`) VALUES ({$this->post_id},'user_phone','{$this->user_phone}')");
        }
    }



    public function get_user_providence()
    {
        if (!$this->user_providence || strlen($this->user_providence) == 0) {
            $this->user_providence = $this->get_meta('user_providence');
        }
        if (!$this->user_providence || strlen($this->user_providence) == 0) {
            $this->user_providence = $this->get_meta('_billing_city');
        }
        return $this->user_providence;
    }
    public function set_user_providence($new_user_providence)
    {
        base::RunQuery("DELETE FROM `post_meta` WHERE `key` = 'user_providence' AND `post_id` = {$this->post_id}");
        if (strlen($new_user_providence) > 0) {
            $this->user_providence = $new_user_providence;
            base::RunQuery("INSERT INTO `post_meta`(`post_id`,`key`,`value`) VALUES ({$this->post_id},'user_providence','{$this->user_providence}')");
        }
    }



    public function get_user_address()
    {
        if (!$this->user_address || strlen($this->user_address) == 0) {
            $this->user_address = $this->get_meta('user_address');
        }
        if (!$this->user_address || strlen($this->user_address) == 0) {
            $this->user_address = $this->get_meta('_billing_address_1');
        }
        return $this->user_address;
    }
    public function set_user_address($new_user_address)
    {
        base::RunQuery("DELETE FROM `post_meta` WHERE `key` = 'user_address' AND `post_id` = {$this->post_id}");
        if (strlen($new_user_address) > 0) {
            $this->user_address = $new_user_address;
            base::RunQuery("INSERT INTO `post_meta`(`post_id`,`key`,`value`) VALUES ({$this->post_id},'user_address','{$this->user_address}')");
        }
    }



    public function get_user_postal_code()
    {
        if (!$this->user_postal_code || strlen($this->user_postal_code) == 0) {
            $this->user_postal_code = $this->get_meta('user_postal_code');
        }
        if (!$this->user_postal_code || strlen($this->user_postal_code) == 0) {
            $this->user_postal_code = $this->get_meta('_billing_postcode');
        }
        return $this->user_postal_code;
    }
    public function set_user_postal_code($new_user_postal_code)
    {
        base::RunQuery("DELETE FROM `post_meta` WHERE `key` = 'user_postal_code' AND `post_id` = {$this->post_id}");
        if (strlen($new_user_postal_code) > 0) {
            $this->user_postal_code = $new_user_postal_code;
            base::RunQuery("INSERT INTO `post_meta`(`post_id`,`key`,`value`) VALUES ({$this->post_id},'user_postal_code','{$this->user_postal_code}')");
        }
    }



    public function get_payment()
    {
        if (!$this->payment || strlen($this->payment) == 0) {
            $this->payment = $this->get_meta('payment');
        }
        if (!$this->payment || strlen($this->payment) == 0) {
            $this->payment = $this->get_meta('_payment_method_title');
        }
        return $this->payment;
    }
    public function set_payment($new_payment)
    {
        base::RunQuery("DELETE FROM `post_meta` WHERE `key` = 'payment' AND `post_id` = {$this->post_id}");
        if (strlen($new_payment) > 0) {
            $this->payment = $new_payment;
            base::RunQuery("INSERT INTO `post_meta`(`post_id`,`key`,`value`) VALUES ({$this->post_id},'payment','{$this->payment}')");
        }
    }



    public function get_shipping()
    {
        if (!$this->shipping || strlen($this->shipping) == 0) {
            $this->shipping = $this->get_meta('shipping');
        }
        return $this->shipping;
    }
    public function set_shipping($new_shipping)
    {
        base::RunQuery("DELETE FROM `post_meta` WHERE `key` = 'shipping' AND `post_id` = {$this->post_id}");
        if (strlen($new_shipping) > 0) {
            $this->shipping = $new_shipping;
            base::RunQuery("INSERT INTO `post_meta`(`post_id`,`key`,`value`) VALUES ({$this->post_id},'shipping','{$this->shipping}')");
        }
    }



    public function get_days()
    {
        if (!$this->days || strlen($this->days) == 0) {
            $this->days = $this->get_meta('days');
        }
        return $this->days;
    }
    public function set_days($new_days)
    {
        base::RunQuery("DELETE FROM `post_meta` WHERE `key` = 'days' AND `post_id` = {$this->post_id}");
        if (strlen($new_days) > 0) {
            $this->days = $new_days;
            base::RunQuery("INSERT INTO `post_meta`(`post_id`,`key`,`value`) VALUES ({$this->post_id},'days','{$this->days}')");
        }
    }



    public function get_user_notes()
    {
        if (!$this->user_notes || strlen($this->user_notes) == 0) {
            $this->user_notes = $this->get_meta('notes');
        }
        return $this->user_notes;
    }
    public function set_user_notes($new_user_notes)
    {
        base::RunQuery("DELETE FROM `post_meta` WHERE `key` = 'notes' AND `post_id` = {$this->post_id}");
        if (strlen($new_user_notes) > 0) {
            $this->user_notes = $new_user_notes;
            base::RunQuery("INSERT INTO `post_meta`(`post_id`,`key`,`value`) VALUES ({$this->post_id},'notes','{$this->user_notes}')");
        }
    }



    public function get_coupon()
    {
        if (!$this->coupon || strlen($this->coupon) == 0) {
            $this->coupon = $this->get_meta('coupon');
        }
        return $this->coupon;
    }
    public function set_coupon($new_coupon)
    {
        base::RunQuery("DELETE FROM `post_meta` WHERE `key` = 'coupon' AND `post_id` = {$this->post_id}");
        if (strlen($new_coupon) > 0) {
            $this->coupon = $new_coupon;
            base::RunQuery("INSERT INTO `post_meta`(`post_id`,`key`,`value`) VALUES ({$this->post_id},'coupon','{$this->coupon}')");
        }
    }



    public function get_coupon_price()
    {
        if (!$this->coupon_price || strlen($this->coupon_price) == 0) {
            $this->coupon_price = $this->get_meta('coupon_price');
        }
        return $this->coupon_price;
    }
    public function set_coupon_price($new_coupon_price)
    {
        base::RunQuery("DELETE FROM `post_meta` WHERE `key` = 'coupon_price' AND `post_id` = {$this->post_id}");
        if (strlen($new_coupon_price) > 0) {
            $this->coupon_price = $new_coupon_price;
            base::RunQuery("INSERT INTO `post_meta`(`post_id`,`key`,`value`) VALUES ({$this->post_id},'coupon_price','{$this->coupon_price}')");
        }
    }



    public function get_items()
    {
        if (!$this->items || count($this->items) == 0) {
            $rows = base::FetchArray("SELECT `item_id`,`qty`,`items_order_id` FROM `items_order` WHERE `order_id` = {$this->post_id}");
            foreach ($rows as $id => $qty)
                $this->items[$id] = $qty;
        }
        return $this->items;
    }
    public function set_item($item)
    {
        $id = $item['id'];
        $qty = $item['qty'];
        $price = $item['price'];
        $total = $item['total'];
        $off = $item['off'];
        $coupon_id = $item['coupon_id'];

        $insert_query = "INSERT INTO `items_order`(`order_id`,`item_id`, `qty`) VALUES ({$this->post_id},$id , $qty);";
        base::RunQuery($insert_query);
        $order_item_id = $this->con->insert_id;
        $insert_meta_query = "INSERT INTO `items_order_meta`(`order_item_id`,`key`,`value`) VALUES ($order_item_id,'price','$price'),($order_item_id,'total','$total'),($order_item_id,'off','$off'),($order_item_id,'coupon_id','$coupon_id');";
        base::RunQuery($insert_meta_query);
    }



    public static function get_item_meta($order_item_id, $key)
    {
        if ($key != "") {
            $shop_meta_query = "SELECT `value` FROM `items_order_meta` WHERE `key` = '$key' AND `order_item_id` = $order_item_id";
            $shop_meta = base::FetchAssoc($shop_meta_query);
        }
        if (!is_null($shop_meta))
            return $shop_meta['value'];
        else
            return "";
    }



    public static function get_item_title($item_id)
    {
        $title = base::FetchAssoc("SELECT `post_title` FROM `post` WHERE `post_id` = $item_id");
        return $title['post_title'];
    }
}
