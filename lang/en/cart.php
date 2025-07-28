<?php

return [
    // Success Messages
    'created_successfully' => 'Cart created successfully',
    'updated_successfully' => 'Cart updated successfully',
    'deleted_successfully' => 'Cart deleted successfully',
    
    // Error Messages
    'creation_failed' => 'Failed to create cart',
    'update_failed' => 'Failed to update cart',
    'deletion_failed' => 'Failed to delete cart',
    'cart_not_found' => 'Cart not found',
    'not_authorized_to_view' => 'You are not authorized to view this cart',
    'not_authorized_to_modify' => 'You are not authorized to modify this cart',
    
    // Validation Messages
    'items_required' => 'Cart items are required',
    'items_must_be_array' => 'Items must be an array',
    'items_min_one' => 'At least one item must be in the cart',
    'product_id_required' => 'Product ID is required',
    'product_id_must_be_integer' => 'Product ID must be an integer',
    'product_not_found' => 'Product not found',
    'quantity_required' => 'Quantity is required',
    'quantity_must_be_integer' => 'Quantity must be an integer',
    'quantity_min_one' => 'Quantity must be at least 1',
    'quantity_max_limit' => 'Quantity cannot be more than 999',
]; 