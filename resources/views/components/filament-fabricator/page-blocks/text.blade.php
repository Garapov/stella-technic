@aware(['page'])

<div class="container mx-auto">
    <div class="py-4 text-grey-600 dark:text-white">
        {!! str($description)->sanitizeHtml() !!}
    </div>
</div>
