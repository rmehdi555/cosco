<?php

return [
    // Authorization Messages
    'not_authorized_to_view' => 'You are not authorized to view this order',
    
    // Success Messages
    'orders_retrieved' => 'Orders list retrieved successfully',
    'order_retrieved' => 'Order details retrieved successfully',
    'created_successfully' => 'Order created successfully',
    
    // Error Messages
    'order_not_found' => 'Order not found',
    'no_orders_found' => 'No orders found',
    'creation_failed' => 'Failed to create order',
    'shipping_address_not_authorized' => 'Shipping address does not belong to you',
    
    // Validation Messages
    'items_required' => 'Order items are required',
    'items_must_be_array' => 'Items must be an array',
    'items_min_one' => 'At least one item must be in the order',
    'product_slug_required' => 'Product slug is required',
    'product_slug_must_be_string' => 'Product slug must be a string',
    'product_not_found' => 'Product not found',
    'quantity_required' => 'Quantity is required',
    'quantity_must_be_integer' => 'Quantity must be an integer',
    'quantity_min_one' => 'Quantity must be at least 1',
    'quantity_max_limit' => 'Quantity cannot be more than 100000',
    'shipping_address_required' => 'Shipping address is required',
    'shipping_address_must_be_integer' => 'Shipping address ID must be an integer',
    'shipping_address_not_found' => 'Shipping address not found',
    'description_must_be_string' => 'Description must be a string',
    'description_max_length' => 'Description cannot be more than 1000 characters',
]; 