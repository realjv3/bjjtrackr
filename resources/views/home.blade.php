@extends('header')

<script type="application/javascript">
    const
        days = @json($days),
        initSettings = @json($settings),
        user = function() {return @json($user);};
</script>

<script type="application/javascript" src="<?= config('app.js_url') ?>/home.bundle.js" defer></script>
