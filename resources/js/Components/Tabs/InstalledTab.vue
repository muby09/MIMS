<script setup>
    import store from '@/store';
    import { ref } from 'vue';
    import TextInput from '@/Components/TextInput.vue';



    const schedules = ref({})
    function loadItem(url = 'installed-list'){
        store.dispatch('getMethod', { url:url }).then((data) => {
        if (data?.status == 200) {
            schedules.value = data.data;
        }
        }).catch(e => {
            console.log(e);
        })
    }
    loadItem()

     const handleKeyup = (event) => {
        
        store.dispatch('getMethod', { url:'search-installed-list/'+event.target.value }).then((data) => {
        if (data?.status == 200) {
            schedules.value = data.data;
        }
        }).catch(e => {
            console.log(e);
        })
    }



</script>


<template>
    <div>
        <div class="overflow-auto  rounded-lg shadow">
                <div>
                    <TextInput
                                            id="longitude"
                                            type="text"
                                            class="mt-1 block w-full"
                                           @keyup="handleKeyup" 
                                            placeholder="enter account number or name"
                                            
                                        />
                </div>
                <table class="min-w-full">
                    <thead class="bg-gray-50 border-b-2 border-gray-200">
                        <tr>
                            <th width ="5%" class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">S/N</th>
                            <th class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">Account Number</th>
                            <th class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">Account Name</th>
                            <th class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">Address</th>
                            <!--<th class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">33Kv feeder</th> -->
                            <th class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">11kv Feeder</th>
                           <th class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">DT Name</th>
                            <!--<th class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">Band</th> -->
                            <!--<th class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">Expected Load (In Amps)</th> -->
                            <th class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">Meter Type</th>
                            <th class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">Meter Number</th>
                            <th class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">Meter Seal</th>
                            <th class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">Latitude</th>
                            <th class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">Longitude</th>
                            <!--<th class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">Connection Status</th> -->
                            <!-- <th class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">contact information</th> -->
                            <th  class="p-3 text-sm font-semibold tracking-wide text-left table-bordered"> 
                               <!--<font-awesome-icon icon="fa-solid fas fa-cog"/> -->
                                Date
                            </th>
                           
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="bg-white" v-for="(item,loop) in schedules" :key="loop">
                            <td class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">{{ loop+1 }}</td>
                            <td class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">{{ item?.account_no }}</td>
                            <td class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">{{ item?.fullname }}</td>
                            <td class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">{{ item?.address }}</td>
                            <!--<td class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">{{ item?.feeder_33 }}</td> -->
                            <td class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">{{ item?.feeder11kv?.name }}</td>
                           <td class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">{{ item?.dt_name }}</td>
                            <!--<td class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">{{ item?.band }}</td>-->
                            <!--<td class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">{{ item?.load }}</td>-->
                            <td class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">{{ item?.meter_type }}</td>
                            <td class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">{{ item?.meter_number }}</td>
                            <td class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">{{ item?.seal }}</td>
                            <td class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">{{ item?.x_cordinate }}</td>
                            <td class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">{{ item?.y_cordinate }}</td>
                            <!--<td class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">{{ item?.connection_status }}</td>-->
                            <!--<td class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">{{ item?.contact }}</td>-->
                           <td class="p-3 text-sm font-semibold tracking-wide text-left table-bordered" >
                                {{ item.date }}
                            </td>
                          
                        </tr>
                    </tbody>
                </table>
                
                
        </div>
    </div>
</template>


<style scoped>

</style>