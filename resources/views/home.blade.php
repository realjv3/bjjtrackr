@extends('header')

<script type="application/javascript">
    const
        days = @json($days),
        initSettings = @json($settings);
</script>

<script type="application/javascript" src="{{config('app.js_url')}}/home.bundle.js" defer></script>
