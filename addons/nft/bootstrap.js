require.config({
    paths: {
		'vue': '../addons/nft/js/vue.min',
    },
    shim: {
        'vue': {
            deps: ['jquery'],
            exports: '$.fn.extend'
        }
    }
});