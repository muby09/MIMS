<script setup>

    import MainLayout from '@/Layouts/MainLayout.vue';
    import { Head,useForm } from '@inertiajs/vue3';
    import InputError from '@/Components/InputError.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import TextInput from '@/Components/TextInput.vue';
import { Inertia } from '@inertiajs/inertia';
    import PaginationLinks from '@/Components/PaginationLinks.vue';

    defineProps({
        data:Array
    })


    const form = useForm({
        region: '',
        file: '',
    });

const submit = () => {
    form.post(route('schedules'), {
        onFinish: () => form.reset(),
    });
};
const changePage = (link) => {
    Inertia.get(link.url, {}, { preserveState: true, preserveScroll: true });
    // store.dispatch('getMethod', { url:url }).then((data) => {
    //     console.log(data);
    //     if (data?.status == 200) {
    //         props.data = data.data;
            
            
    //     }
    //     }).catch(e => {
    //         console.log(e);
    //     })
};


</script>

<template>
     <Head title="Schedules" />

    <MainLayout>
        <div class="px-4 py-5">
             <form @submit.prevent="submit">
                <div class="grid grid-col-2 gap-2">
                    <div>
                        <div class="flex justify-between">
                            <InputLabel for="file" value="Excel File" />
                            <a href="/files/images/Approved Shedule template.xlsx" class="text-optimal font-bold">Download File</a>
                        </div>
                        <TextInput
                            id="file"
                            type="file"
                            class="mt-1 block w-full"
                            @input="form.file = $event.target.files[0]"
                            autofocus
                        />
                        <InputError class="mt-2" :message="form.errors.file" />
                    </div>
                    
                    
                    <div class=" justify-end mb-1 ">
                        <button  @click="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class=" bg-optimal text-white px-4 py-2 rounded mr-2">Submit</button>
                    </div>
            </div>
        </form>

        <div class="overflow-auto rounded-lg shadow">
           
                <table class="w-full">
                    <thead class="bg-gray-50 border-b-2 border-gray-200">
                        <tr>
                            <th width ="5%" class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">S/N</th>
                            <th class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">Region</th>
                            <th class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">allocated_meter_number</th>
                            <th class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">account_number</th>
                            <th class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">meter_type</th>
                            <th class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">map</th>
                            <th class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">customer_name</th>
                            <th class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">address</th>
                            <th class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">phone_no</th>
                            <th class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">feeder_name</th>
                            <th class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">business_unit</th>
                            <th class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">state</th>
                            <th class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">total_billings</th>
                            <th class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">total_settlement</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="bg-white" v-for="(item,loop) in data?.data" :key="loop">
                            <td class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">{{ loop+1 }}</td>
                            <td class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">{{ item?.region?.region }}</td>
                            <td class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">{{ item?.allocated_meter_number }}</td>
                            <td class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">{{ item?.account_number }}</td>
                            <td class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">{{ item?.meter_type }}</td>
                            <td class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">{{ item?.map }}</td>
                            <td class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">{{ item?.customer_name }}</td>
                            <td class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">{{ item?.address }}</td>
                            <td class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">{{ item?.phone_no }}</td>
                            <td class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">{{ item?.feeder_name }}</td>
                            <td class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">{{ item?.business_unit }}</td>
                            <td class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">{{ item?.state }}</td>
                            <td class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">{{ item?.total_billings }}</td>
                            <td class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">{{ item?.total_settlement }}</td>
                          <!--  <td class="p-3 text-sm font-semibold tracking-wide text-left table-bordered" >
                                <button class="p-1 oy-1 text-sm bg-yellow-500 text-white me-2 inline-block" @click="editItem(item)">Edit</button>
                            </td> -->
                        </tr>
                    </tbody>
                </table>
                
                
        </div>
        <div class="mt-4">
            <div class="flex space-x-1">
                <pagination-links v-for="(link, i) of data.links" :link="link" :key="i"
                    @next="changePage($event,link)"></pagination-links>
            </div>
        </div>
        </div>
    </MainLayout>
</template>



<style lang="scss" scoped>

</style>