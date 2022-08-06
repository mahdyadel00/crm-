@foreach($fields as $field)
<div class="x-element">
    <div class="x-each">
        <div class="x-title"><span class="x-highlight">{{ $field->customfields_title }}</span></div>

        <div class="x-content">
            {{ customFieldValue($field->customfields_name, $lead, 'text') }}
        </div>
    </div>
</div>
@endforeach