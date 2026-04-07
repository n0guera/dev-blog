<?php

namespace App\Http\Requests;

use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostRequest extends FormRequest
{
    public function authorize(): bool
    {
        $post = $this->route('post');

        if ($this->isMethod('post')) {
            return $this->user() && $this->user()->isAdmin();
        }

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            return $post && $this->user() && $this->user()->isAdmin() && $post->user_id === $this->user()->id;
        }

        return false;
    }

    public function rules(): array
    {
        $post = $this->route('post');

        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'featured_image' => ['nullable', 'string', 'max:255'],
            'status_id' => ['required', 'integer', Rule::exists('post_statuses', 'id')],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', Rule::exists('tags', 'id')],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The post title is required.',
            'title.max' => 'The title may not be greater than 255 characters.',
            'content.required' => 'The post content is required.',
            'excerpt.max' => 'The excerpt may not be greater than 500 characters.',
            'status_id.required' => 'Please select a post status.',
            'status_id.exists' => 'The selected status is invalid.',
        ];
    }
}
