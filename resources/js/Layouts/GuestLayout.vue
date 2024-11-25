<script setup>
    import { Link } from '@inertiajs/vue3';
    import Flash from '@/Components/Flash.vue';
    import ApplicationLogo from '@/Components/ApplicationLogo.vue';
    import SpinnerLoader from '@/components/SpinnerLoader.vue';
    import Footer from '@/Components/Footer.vue';
</script>

<template>
        <section class="wrapper ">
                <div class="header justify-between px-10 z-50 flex ">
                    <div class="logo">
                        <Link href="/">
                            <ApplicationLogo class="block h-9 w-auto fill-current text-gray-800" />
                        </Link>
                       <!-- <img src="/files/images/momas logo.jpg" alt="Momas logo"> -->
                    </div>
                    <div class="links z-50">
                        <ul>
                            <Link  :href="route('login')" class="link bg-gray-600">Login</Link>
                        </ul>
                    </div>
                </div>
         <div class="main-content  container flex flex-col justify-between mx-auto sm:flex-row">
            <div class="w-full">
                <div class="content z-50">
                     <Flash/>
                            <SpinnerLoader/>
                            <slot class="z-50" />
                </div>
            </div>
             <div class="z-50">
                <div>
                    <img src="/files/images/tvati.png" alt="Smart Meter" class="w-30">
                
                </div>
             </div>
            </div>
            
            
            
        </section>
        <Footer class="mt-auto"/>

        
   
     
</template>

<style scoped>

    :root{
        --mainColor:#64bcf4;
        --hoverColor:#5bacdf;
        --backgroundColor:#f1f8fc;
        --darkOne:#312f3a;
        --darkTwo:#45424b;
        --lightOne:#919191;
        --lightTwo:#aaa;
    }

 

.wrapper {
    position: relative;
    width: 100%;
    min-height: 95vh !important;
    align-items: center;
    padding:  0;
    overflow: hidden;
    background: #f1f1f1;
    /* background: #919191; */
    /* background: #EDFBE2; */
    /* background: #54595F; */

}

.wrapper::before{
    content: '';
    position: absolute;
    width: 2000px;
    height: 2000px;
    border-radius: 50%;
    background: linear-gradient(-45deg, #919191, #54595F);
    top: -10%;
    left: 48%;
    transform: translateY(-50%);
    z-index: 1;
}

.header{
    padding: 30px 8% 0;

}

.main-content {
    position: relative;
    width: 100%;
    min-height: calc(100vh -40px);
    padding: 50px 8% 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
}



.content {
    max-width: 630px;
    z-index: 100;
}


ul .link{
    /* color: #222;
    text-decoration: none;
    font-size: 18px;
    font-weight: 500;
    margin: 0 20px;
    transition: .3s; */

    display: inline-block;
    padding: 10px 28px;
    /* background: #EDFBE2; */
    /* background: #04befe; */
    border: 2px solid #EDFBE2;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, .1);

    font-size: 16px;
    color: #f1f8fc;
    letter-spacing: 1px;
    text-decoration: none;
    font-weight: 600;
    transition: .5s;
}

@media (max-width: 756px) {


}

</style>