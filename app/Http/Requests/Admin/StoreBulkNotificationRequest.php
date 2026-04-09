<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreBulkNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:1000'],
            'topic' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'data' => ['nullable', 'array'],
            'is_scheduled' => ['nullable', 'boolean'],
            'scheduled_at' => ['nullable', 'date', 'after:now'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'عنوان الإشعار مطلوب.',
            'body.required' => 'محتوى الإشعار مطلوب.',
            'topic.required' => 'الـ topic مطلوب.',
            'scheduled_at.after' => 'وقت الجدولة يجب أن يكون في المستقبل.',
        ];
    }
}
