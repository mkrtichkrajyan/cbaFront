<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'The :attribute must be accepted.',
    'active_url' => ':attribute - ը վավեր URL չէ:',
    'after' => 'The :attribute must be a date after :date.',
    'after_or_equal' => 'The :attribute must be a date after or equal to :date.',
    'alpha' => ':attribute - ը կարող է պարունակել միայն տառեր:',
    'alpha_dash' => 'The :attribute - ը կարող է պարունակել միայն տառեր, թվեր, եւ գծիկներ.',
    'alpha_num' => ':attribute - ը կարող է պարունակել միայն տառեր եւ թվեր:',

    'array' => 'The :attribute must be an array.',
    'before' => 'The :attribute must be a date before :date.',
    'before_or_equal' => 'The :attribute must be a date before or equal to :date.',
    'between' => [
        'numeric' => 'The :attribute must be between :min and :max.',
        'file' => 'The :attribute must be between :min and :max kilobytes.',
        'string' => 'The :attribute must be between :min and :max characters.',
        'array' => 'The :attribute must have between :min and :max items.',
    ],
    'boolean' => 'The :attribute field must be true or false.',
    'confirmed' => 'The :attribute confirmation does not match.',
    'date' => 'The :attribute is not a valid date.',
    'date_format' => 'The :attribute does not match the format :format.',
    'different' => 'The :attribute and :other must be different.',
    'digits' => 'The :attribute must be :digits digits.',
    'digits_between' => 'The :attribute must be between :min and :max digits.',
    'dimensions' => 'The :attribute has invalid image dimensions.',
    'distinct' => 'The :attribute field has a duplicate value.',
    'email' => ':attribute - ը պետք է լինի վավեր էլ. փոստի հասցե:',
    'exists' => 'ընտրված - ը :attribute անվավեր է:',
    'file' => ':attribute - ը պետք է լինի ֆայլ:',
    'filled' => ':attribute դաշտը պետք է արժեք ունենա:',
    'image' => ':attribute - ը պետք է լինի նկար:',
    'in' => 'The selected :attribute is invalid.',
    'in_array' => 'The :attribute field does not exist in :other.',
    'integer' => ':attribute - ը պետք է լինի ամբողջ թիվ.',
    'ip' => 'The :attribute must be a valid IP address.',
    'ipv4' => 'The :attribute must be a valid IPv4 address.',
    'ipv6' => 'The :attribute must be a valid IPv6 address.',
    'json' => 'The :attribute must be a valid JSON string.',
    'max' => [
        'numeric' => ':attribute - ը չի կարող լինել ավելի մեծ, քան :max.',
        'file' => 'The :attribute may not be greater than :max kilobytes.',
        'string' => 'The :attribute may not be greater than :max characters.',
        'array' => 'The :attribute may not have more than :max items.',
    ],
    'mimes' => 'The :attribute must be a file of type: :values.',
    'mimetypes' => 'The :attribute must be a file of type: :values.',
    'min' => [
        'numeric' => ':attributee - ը պետք է լինի առնվազն :min.',
        'file' => ':attribute- ը պետք է լինի առնվազն :min կիլոբայթ.',
        'string' => ':attribute պետք է լինի առնվազն :min նիշ.',

        'array' => 'The :attribute must have at least :min items.',
    ],
    'not_in' => 'The selected :attribute is invalid.',
    'numeric' => ':attribute դաշտը պետք է լինի թիվ',
    'present' => 'The :attribute field must be present.',
    'regex' => 'The :attribute format is invalid.',
    'required' => ':attribute դաշտը պարտադիր է',
    'required_if' => 'The :attribute field is required when :other is :value.',
    'required_unless' => 'The :attribute field is required unless :other is in :values.',
    'required_with' => 'The :attribute field is required when :values is present.',
    'required_with_all' => 'The :attribute field is required when :values is present.',
    'required_without' => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same' => 'The :attribute and :other must match.',
    'size' => [
        'numeric' => 'The :attribute must be :size.',
        'file' => 'The :attribute must be :size kilobytes.',
        'string' => 'The :attribute must be :size characters.',
        'array' => 'The :attribute must contain :size items.',
    ],
    'string' => ':attribute դաշտը պետք է լինի տող',
    'timezone' => 'The :attribute must be a valid zone.',
    'unique' => 'The :attribute - ը արդեն կա',
    'uploaded' => ':attribute - ը  չհաջողվեց վերբեռնել',
    'url' => ':attribute դաշտի ձեւաչափը անվավեր է:',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],

        'password' => [
            'required' => 'Ծածկագիր դաշտը պարտադիր է ',
            'min' => 'Ծածկագիր դաշտը պետք է լինի առնվազն :min նիշ. ',
            'confirmed' => 'Ծածկագրերը չեն համընկնում ',

        ],
        'company_website' => [
            'url' => 'Կազմակերպության Կայք դաշտի ձեւաչափը անվավեր է',
        ],
        'choose_img' => [
            'image' => 'Կազմակերպության լոգոն պետք է լինի նկար',
        ],
        'company_type' => [
            'required' => 'Կազմակերպության տեսակը դաշտը պարտադիր է ',
        ],
        'company_name' => [
            'required' => 'Կազմակերպության անվանումը դաշտը պարտադիր է ',
            'min' => 'Կազմակերպության անվանումը դաշտը պետք է լինի առնվազն 3 նիշ. ',

        ],
        'superadmin_name' => [
            'required' => 'Սուպերադմինի անուն դաշտը պարտադիր է ',
            'min' => 'Սուպերադմինի անուն դաշտը պետք է լինի առնվազն 3 նիշ. ',
        ],
        'superadmin_lastname' => [
            'required' => 'Սուպերադմինի Ազգանուն դաշտը պարտադիր է ',
            'min' => 'Սուպերադմինի Ազգանուն դաշտը պետք է լինի առնվազն 3 նիշ. ',
        ],
        'email' => [
            'required' => 'Էլ․ հասցե դաշտը պարտադիր է ',
            'email' => 'Էլ․ հասցե դաշտը պետք է լինի վավեր էլ. փոստի հասցե:',
            'unique' => 'Էլ․ հասցեն արդեն կա',
        ],

        'name' => [
            'required' => 'Անուն դաշտը պարտադիր է ',
            'min' => 'Անուն դաշտը պետք է լինի առնվազն 3 նիշ. ',
        ],
        'car_type' => [
            'required' => 'Ավտոմեքենա դաշտը պարտադիր է ',

            'integer' => 'Ավտոմեքենա դաշտը  անվավեր է',
        ],

        'prepayment' => [
            'numeric' => 'Կանխավճար դաշտը պետք է լինի թիվ ',
        ],
        'car_cost' => [
            'required' => 'Ավտոմեքենայի արժեք դաշտը պարտադիր է ',

            'numeric' => 'Ավտոմեքենայի արժեք դաշտը պետք է լինի թիվ',
        ],
        'loan_term' => [
            'required' => 'Ժամկետ դաշտը պարտադիր է ',

            'numeric' => 'Ժամկետ դաշտը պետք է լինի թիվ',
        ],


    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
