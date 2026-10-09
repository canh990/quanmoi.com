<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBlogRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Blog::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable'],
            'category_name' => ['nullable', 'string', 'max:100'],
            'new_category' => ['nullable', 'string', 'max:100'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'], // 5MB Max
            'tags' => ['nullable', 'array'],
            'custom_tags' => ['nullable', 'string', 'max:255'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            // Action can be 'draft' or 'pending' (submit for review)
            'action' => ['required', 'string', Rule::in(['draft', 'pending'])],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Vui lòng nhập tiêu đề bài viết.',
            'title.max' => 'Tiêu đề bài viết không được vượt quá 255 ký tự.',
            'category_id.required' => 'Vui lòng chọn danh mục bài viết.',
            'category_id.exists' => 'Danh mục bài viết được chọn không hợp lệ.',
            'content.required' => 'Nội dung bài viết không được để trống.',
            'cover_image.image' => 'Ảnh bìa phải là định dạng hình ảnh hợp lệ (jpg, png, webp).',
            'cover_image.mimes' => 'Ảnh bìa phải thuộc định dạng: jpeg, png, jpg, gif hoặc webp.',
            'cover_image.max' => 'Dung lượng ảnh bìa không được vượt quá 5MB.',
            'action.required' => 'Thao tác đăng bài không hợp lệ.',
            'action.in' => 'Hành động gửi bài không hợp lệ.',
        ];
    }
}
