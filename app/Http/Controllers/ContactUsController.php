<?php

namespace App\Http\Controllers;

use App\Models\ContactUs;
use App\Http\Requests\ContactUsRequest;
use App\Http\Resources\ContactUsResource;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Contact Us",
 *     description="Contact Us API endpoints"
 * )
 *
 * @OA\Schema(
 *     schema="ContactUsResource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="first_name", type="string", example="علی"),
 *     @OA\Property(property="last_name", type="string", example="احمدی"),
 *     @OA\Property(property="full_name", type="string", example="علی احمدی"),
 *     @OA\Property(property="email", type="string", format="email", example="ali@example.com"),
 *     @OA\Property(property="phone", type="string", example="09123456789"),
 *     @OA\Property(property="body", type="string", example="سلام، من سوالی در مورد محصولات شما دارم."),
 *     @OA\Property(property="is_answered", type="boolean", example=false),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-12-19 10:30:00"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-12-19 10:30:00")
 * )
 */
class ContactUsController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/contact-us",
     *     summary="Submit contact us form",
     *     tags={"Contact Us"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"body"},
     *             @OA\Property(property="first_name", type="string", example="علی"),
     *             @OA\Property(property="last_name", type="string", example="احمدی"),
     *             @OA\Property(property="email", type="string", format="email", example="ali@example.com"),
     *             @OA\Property(property="phone", type="string", example="09123456789"),
     *             @OA\Property(property="body", type="string", example="سلام، من سوالی در مورد محصولات شما دارم.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Contact message submitted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="پیام شما با موفقیت ارسال شد."),
     *             @OA\Property(property="data", ref="#/components/schemas/ContactUsResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(ContactUsRequest $request): JsonResponse
    {
        $contactUs = ContactUs::create($request->validated());

        return ApiResponse::success(
            new ContactUsResource($contactUs),
            trans('contact.submitted_success'),
            201
        );
    }
}
