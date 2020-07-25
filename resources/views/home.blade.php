@extends('header')

<script type="application/javascript">
    const
        user = function() {return @json($user);},
        initSettings = @json($settings);
</script>

<script type="application/javascript" src="<?= config('app.js_url') ?>/home.bundle.js" defer></script>
