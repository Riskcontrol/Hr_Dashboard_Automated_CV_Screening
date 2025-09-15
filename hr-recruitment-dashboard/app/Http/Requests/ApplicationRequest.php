<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApplicationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'applicant_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z\s\-\.\']+$/' // Only letters, spaces, hyphens, dots, apostrophes
            ],
            'applicant_email' => [
                'required',
                'email:rfc,dns',
                'max:255',
                'unique:applications,applicant_email'
            ],
            'phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[\+]?[0-9\-\(\)\s]+$/'
            ],
            'cv_file' => [
                'required',
                'file',
                'mimes:pdf,doc,docx',
                'max:3072', // 3MB
                'uploaded'
            ],
            'job_position' => [
                'required',
                'exists:keyword_sets,id,is_active,1'
            ]
        ];
    }

    public function messages()
    {
        return [
            'applicant_name.regex' => 'Name can only contain letters, spaces, hyphens, dots, and apostrophes.',
            'applicant_email.unique' => 'This email has already submitted an application.',
            'phone.regex' => 'Please enter a valid phone number.',
            'cv_file.mimes' => 'Only PDF, DOC, and DOCX files are allowed.',
            'cv_file.max' => 'File size must not exceed 3MB.',
            'job_position.exists' => 'Selected job position is not available.'
        ];
    }
}
