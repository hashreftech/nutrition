var config = {
    map: {
        '*': {
            owl_carousel: 'Tridhyatech_Slider/js/owl.carousel.min'
            //owl_config: 'Tridhyatech_Slider/js/owl.config'
        }
    },
    shim: {
       /* owl_carousel: {
            deps: ['jquery']
        },*/
        owl_carousel: {
            deps: ['jquery','owl_carousel']
        }
    }
};
