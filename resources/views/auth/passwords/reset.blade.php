@extends('header')
<script type="application/javascript">
    const
        email = '{{ $email }}',
        token = '{{ $token }}';
</script>
<script type="application/javascript" src="{{config('app.js_url')}}/reset.bundle.js" defer></script>
