<template>
    <div >
           
        <div class="alert" v-if="$page.props.flash && $page.props.flash.message">
            <div class="p-2 bg-blue-500 items-center text-indigo-100 leading-none lg:rounded-full flex lg:inline-flex px-4 py-3" role="alert">
                <span class="font-semibold mr-2  px-8 text-left flex-auto w-full">{{ $page.props.flash.message }}</span>
            </div>
        </div>
        <div class="alert" v-if="$page.props.flash && $page.props.flash.error">
            <div class="bg-red-500 items-center text-indigo-100 px-4 py-3 rounded relative lg:rounded-full flex lg:inline-flex" role="alert">
                <span class="font-semibold mr-2 px-8 text-left flex-auto w-full">{{ $page.props.flash.error }}</span>
            </div>
        </div>

        <div class="alert" v-if="$page.props.flash && $page.props.flash.warning">
            <div class="bg-yellow-500 items-center text-indigo-100 px-4 py-3 rounded relative lg:rounded-full flex lg:inline-flex" role="alert">
                <span class="font-semibold mr-2  px-8 text-left flex-auto w-full">{{ $page.props.flash.warning }}</span>
            </div>
        </div>
        <div class="alert" v-if="$page.props.flash && $page.props.flash.success">
            <div class="p-2 bg-green-500 items-center text-indigo-100 leading-none lg:rounded-full flex lg:inline-flex px-4 py-3" role="alert">
                <span class="font-semibold mr-2  px-8 text-left flex-auto w-full">{{ $page.props.flash.success }}</span>
            </div>
        </div>
    </div>

    
</template>

<script setup>

</script>