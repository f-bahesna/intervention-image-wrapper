<?php

/**
 * @author frada <fbahezna@gmail.com>
 */

if(!function_exists('imagewrapper')) {
    function imagewrap(){
        return app(\Fbahesna\InterventionImageWrapper\Services\ImageWrapperService::class);
    }
}