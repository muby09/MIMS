<script setup>
import AuthHeader from '@/Components/AuthHeader.vue';
import Footer from '@/Components/Footer.vue';
import SideBar from '@/Components/SideBar.vue';
import Flash from '@/Components/Flash.vue';
import { onMounted } from 'vue';

 onMounted(() => {
        let navName = document.querySelectorAll(".nav-link");
        for (let i = 0; i < navName.length; i++) {
            navName[i].addEventListener("click", () => {
                if(window.innerWidth < 756){
                    sideBar.classList.toggle('showSidebar')
                }
            })
        }
        let sidebarBtn = document.querySelector('#toggleSideBar')
        let sideBar = document.querySelector('.sideBar')
        sidebarBtn.addEventListener('click', () => {
            sideBar.classList.toggle('showSidebar')
        })
    })


</script>
<template>
    <!-- component -->
    <div class="h-screen flex">
        <spinner-loader></spinner-loader>
    <!-- Sidebar -->
        <SideBar/>
            <main class="flex min-h-[90vh] flex-col w-full overflow-x-hidden overflow-y-auto ">
            <!-- Header-->
            <AuthHeader/>
            <!--content-->
                <div class="flex w-full mx-auto px-6 py-1">
                    <div class="flex flex-col w-full h-full">
                        <div class="mx-auto"><Flash/></div>
                        <slot/>
                    </div>
                </div>
                <!--- footer -->
             <Footer class="mt-auto"/>
        </main>
    </div>
</template>



<style scoped>
 ::-webkit-scrollbar {
     width: 5px;
     height: 5px;
 }

 ::-webkit-scrollbar-thumb {
     background: linear-gradient(13deg, #7bcfeb 14%, #e685d3 64%);
     border-radius: 10px;
 }

 ::-webkit-scrollbar-thumb:hover {
     background: linear-gradient(13deg, #c7ceff 14%, #f9d4ff 64%);
 }

 ::-webkit-scrollbar-track {
     background: #ffffff;
     border-radius: 10px;
     box-shadow: inset 7px 10px 12px #f0f0f0;
 }
</style>