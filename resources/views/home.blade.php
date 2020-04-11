@extends('header')

<script type="application/javascript">
    const
        user = function() {return @json($user);},
        settings = @json($settings);
</script>

<script type="application/javascript" src="https://localhost:9000/home.bundle.js" defer></script>
