<?php

return [
    'first_name_string' => 'The first name must be a string.',
    'first_name_max' => 'The first name may not be greater than 255 characters.',
    'last_name_string' => 'The last name must be a string.',
    'last_name_max' => 'The last name may not be greater than 255 characters.',
    'avatar_image_image' => 'The file must be an image.',
    'avatar_image_mimes' => 'The image must be a file of type: jpeg, png, jpg, gif.',
    'avatar_image_max' => 'The image may not be greater than 2MB.',
    'password_string' => 'The password must be a string.',
    'password_min' => 'The password must be at least 8 characters.',
    'password_confirmed' => 'The password confirmation does not match.',
    'password_confirmation_required_with' => 'The password confirmation is required when password is present.',
    'password_confirmation_string' => 'The password confirmation must be a string.',
    'password_confirmation_min' => 'The password confirmation must be at least 8 characters.',
    
    // RegisterRequest validation messages
    'email.required' => 'The email field is required.',
    'email.string' => 'The email must be a string.',
    'email.email' => 'The email must be a valid email address.',
    'email.max' => 'The email may not be greater than 255 characters.',
    'email.unique' => 'The email has already been taken.',
    'cell_phone.required' => 'The cell phone field is required.',
    'cell_phone.string' => 'The cell phone must be a string.',
    'cell_phone.unique' => 'The cell phone has already been taken.',
    'password.required' => 'The password field is required.',
    'password.min' => 'The password must be at least 6 characters.',
    
    // LoginRequest validation messages
    'email_format' => 'The email format is invalid.',
    'password_required' => 'The password is required.',
    'password_string' => 'The password must be a string.',
    'identifier_required' => 'Please enter your email or cell phone number.',
]; 