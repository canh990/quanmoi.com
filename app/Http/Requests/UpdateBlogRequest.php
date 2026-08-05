<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBlogRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('blog'));
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
            'category_id' => ['required', 'exists:blog_categories,id'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'], // 5MB Max
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:blog_tags,id'],
            // Action can be 'draft' or 'pending' (submit for review)
            'action' => ['required', 'string', Rule::in(['draft', 'pending'])],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Tiêu đề không được để trống.',
            'category_id.required' => 'Vui lòng chọn danh mục.',
            'content.required' => 'Nội dung bài viết không được để trống.',
            'cover_image.image' => 'Ảnh bìa phải là định dạng hình ảnh.',
            'cover_image.max' => 'Dung lượng ảnh bìa không được vượt quá 5MB.',
        ];
    }
}
