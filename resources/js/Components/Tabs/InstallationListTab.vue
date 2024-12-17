<script setup>
    import store from '@/store';
    import { ref } from 'vue';
    import Modal from '@/Components/Modal.vue';
    import InputError from '@/Components/InputError.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import TextInput from '@/Components/TextInput.vue';
    import BaseSelect from '@/Components/BaseSelect.vue';
    import { formatError } from "@/composables/formatError";
    const { transformValidationErrors } = formatError()

    const showModal = ref(false)
    const closeModal = () => {
        resetForm()
        showModal.value = false;
    }

    
    const showForm = (data) => {
        showModal.value = true;
        meterForm.value.meter_number = data.allocated_meter_number
        meterForm.value.fullname = data.customer_name
        meterForm.value.dt_name = data.dt_name
        meterForm.value.account_no = data.account_number
        meterForm.value.address = data.address
        meterForm.value.business_unit = data.business_unit
        meterForm.value.gsm = data.phone_no
    }
   
// meter_type

// address

// feeder_name


    const tariffs = [
        {"id":  "R1", "text": "R1"},
        {"id":  "R2", "text": "R2"},
        {"id":  "R3", "text": "R3"},
        {"id":  "C1", "text": "C1"},
        {"id":  "C2", "text": "C2"},
        {"id":  "D1", "text": "D1"},
        {"id":  "D2", "text": "D2"},
        {"id":  "A1", "text": "A1"},
        {"id":  "A2", "text": "A2"},
    ]
    const premises = [
        {"id":  "RESIDENTIAL", "text": "RESIDENTIAL"},
        {"id": "COMMERCIAL", "text": "COMMERCIAL"},
        {"id": "SPECIAL", "text": "SPECIAL"},
    ]
  
    
    const meterForm = ref({
        'meter_number' :'', 
        'preload':25, 
        'state':'' , 
        'doi':'', 
        'dt_name':'',
        'dt_type':'',
        'upriser':'' ,
        'pole':'' ,
        'tariff':'' ,
        'advtariff':'' ,
        'fullname':'' ,
        'gsm':'' ,
        'email' :'',
        'premises':'' ,
        'phase':'',
        'address':'',
        'remark':'',
        'feeder_33kv':'',
        'feeder_11kv':'',
        'meter_type':'',
        'meter_brand':'',
        'meter_tech' :'',
        'estimated' :'',
        'account_no':'' ,
        'business_unit':'',
        'x_cordinate':'',
        'y_cordinate':'' ,
        'installer':'' ,
        'supervisor':'' ,
        'rf_channel':'' ,
        'din':'' ,
        'seal':'' ,
        'dt_code':'',
        'zone':'',
        errors:{}
    })

    const recordForm = () => {
        meterForm.value.errors = {}
        store.dispatch('postMethod', { url: '/record-form', param: meterForm.value }).then((data ) => {
        if (data?.status == 422) {
            meterForm.value.errors = transformValidationErrors(data.data)
        } else if (data?.status == 201) {
            closeModal()
            loadItem()
        }
        }).catch(e => {
            console.log(e);
        })
    }

    const resetForm = () => {
        meterForm.value = {
        'meter_number': '' , 
        'preload': 25 , 
        'state': ''  , 
        'doi': '' , 
        'dt_name': '' ,
        'dt_type': '' ,
        'upriser': ''  ,
        'pole': ''  ,
        'tariff': ''  ,
        'advtariff': ''  ,
        'fullname': ''  ,
        'gsm': ''  ,
        'email': ''  ,
        'premises': '',
        'phase': '' ,
        'address': '' ,
        'remark': '' ,
        'feeder_33kv': '' ,
        'feeder_11kv': '' ,
        'meter_type': '' ,
        'meter_brand': '' ,
        'meter_tech': ''  ,
        'estimated': ''  ,
        'account_no': ''  ,
        'business_unit': '' ,
        'x_cordinate': '' ,
        'y_cordinate': ''  ,
        'installer': ''  ,
        // 'supervisor': ''  ,
        'rf_channel': ''  ,
        'din': ''  ,
        'seal': ''  ,
        'dt_code': '' ,
        'zone': '' ,
        errors:{}
        }
    }
   


    loadState()
    const states = ref({})
    function loadState() {
        store.dispatch('loadDropdown', 'zone-state').then(({ data }) => {
            states.value = data;
        }).catch(e => {
            console.log(e);
        })
    }

    const zones = ref({})
    function loadStateZone(id) {
        store.dispatch('loadDropdown', 'zone/'+id).then(({ data }) => {
            zones.value = data;
        }).catch(e => {
            console.log(e);
        })
    }

    load33kvFeeder
     const feeder33s = ref({})
    function load33kvFeeder(id) {
        store.dispatch('loadDropdown', 'feeder-33/'+id).then(({ data }) => {
            feeder33s.value = data;
        }).catch(e => {
            console.log(e);
        })
    }
     const feeder11s = ref({})
    function load11kvFeeder(id) {
        store.dispatch('loadDropdown', 'feeder-11/'+id).then(({ data }) => {
            feeder11s.value = data;
        }).catch(e => {
            console.log(e);
        })
    }

    meterBrand()
    const brands = ref({})
    function meterBrand() {
        store.dispatch('loadDropdown', 'meter-brands').then(({ data }) => {
            brands.value = data;
        }).catch(e => {
            console.log(e);
        })
    }

    meterType()
    const types = ref({})
    function meterType() {
        store.dispatch('loadDropdown', 'meter-types').then(({ data }) => {
            types.value = data;
        }).catch(e => {
            console.log(e);
        })
    }
    loadInstallers()
    const installers = ref({})
    function loadInstallers() {
        store.dispatch('loadDropdown', 'installers').then(({ data }) => {
            installers.value = data;
        }).catch(e => {
            console.log(e);
        })
    }


    const schedules = ref({})
    function loadItem(url = 'schedule-list'){
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
        
        store.dispatch('getMethod', { url:'search-schedule-list/'+event.target.value }).then((data) => {
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
        
        <Modal :show="showModal" @close="closeModal" max-width="6xl" title="Record Data" @submit="recordForm">
           <form action="" >
                    <div class="py-4 px-4">
                            <div class="grid gap-4  text-sm grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 ">
                                <div class="flex flex-col ">
                                    <InputLabel for="first_name" value="Meter Number" />
                                        <TextInput
                                            id="meter_number"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="meterForm.meter_number"
                                            placeholder="Meter Number"
                                            required
                                        />
                                        <InputError class="mt-2" :message="meterForm?.errors?.meter_number" />
                                </div>

                                <div class="grid grid-cols-1  md:grid-cols-2 gap-2">
                                     <div class="flex flex-col ">
                                        <InputLabel for="doi" value="Date" />
                                            <TextInput
                                                id="doi"
                                                type="date"
                                                class="mt-1 block w-full"
                                                v-model="meterForm.doi"
                                               
                                            />
                                            <InputError class="mt-2" :message="meterForm?.errors?.doi" />
                                    </div>
                                    <div class="flex flex-col ">
                                        <InputLabel for="preload" value="Pre load unit" />
                                            <TextInput
                                                id="preload"
                                                type="number"
                                                class="mt-1 block w-full"
                                                v-model="meterForm.preload"
                                                placeholder="e.g 15"
                                               
                                            />
                                            <InputError class="mt-2" :message="meterForm?.errors?.preload" />
                                    </div>
                                </div>

                                <div class="grid grid-cols-1  md:grid-cols-2 gap-2">
                                     <div class="flex flex-col ">
                                              <InputLabel for="state" value="State" />
                                                <select v-model="meterForm.state" @change="loadStateZone($event.target.value)" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                                    <option value="" selected>Choose State</option>
                                                    <option v-for="option in states" :key="option.id" :value="option.id">
                                                        {{ option.text }}
                                                    </option>
                                                </select> 
                                            <InputError class="mt-2" :message="meterForm?.errors?.state" /> 
                                    </div>
                                    <div class="flex flex-col ">
                                           
                                            <InputLabel for="zone" value="Trading Zone" />
                                                <select v-model="meterForm.zone" @change="load33kvFeeder($event.target.value)" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                                    <option value="" selected>Choose State</option>
                                                    <option v-for="option in zones" :key="option.id" :value="option.id">
                                                        {{ option.text }}
                                                    </option>
                                                </select> 
                                                <InputError class="mt-2" :message="meterForm?.errors?.zone" />  
                                    </div>
                                </div>

                                <!-- next row  -->

                                <!--<div class="grid grid-cols-1  md:grid-cols-2 gap-2">

                                        
                                    <div class="flex flex-col ">
                                        <InputLabel for="dt_code" value="DT Number" />
                                            <TextInput
                                                id="dt_code"
                                                type="number"
                                                class="mt-1 block w-full"
                                                v-model="meterForm.dt_code"
                                                placeholder="e.g 04"
                                            />
                                            <InputError class="mt-2" :message="meterForm?.errors?.dt_code" />
                                    </div>

                                    <div class="flex flex-col ">
                                            <InputLabel for="dt_capacity" value="DT Capacity" />
                                            <TextInput
                                                id="dt_capacity"
                                                type="number"
                                                class="mt-1 block w-full"
                                                v-model="meterForm.dt_capacity"
                                                placeholder="DT Capacity"
                                            />
                                            <InputError class="mt-2" :message="meterForm?.errors?.dt_capacity" />
                                    </div>
                                    
                                </div> -->

                               

                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-5 gap-2"> 
                            <div class="flex flex-col ">
                                        <InputLabel for="dt_name" value="DT Name" />
                                            <TextInput
                                                id="dt_name"
                                                type="text"
                                                class="mt-1 block w-full"
                                                v-model="meterForm.dt_name"
                                                placeholder="e.g 04"
                                            />
                                            <InputError class="mt-2" :message="meterForm?.errors?.dt_name" />
                                    </div>
                                     <div class="flex flex-col ">
                                        <InputLabel for="upriser" value="Upriser" />
                                            <TextInput
                                                id="upriser"
                                                type="number"
                                                class="mt-1 block w-full"
                                                v-model="meterForm.upriser"
                                                placeholder="e.g 02"
                                                required
                                            />
                                            <InputError class="mt-2" :message="meterForm?.errors?.upriser" />
                                    </div>

                                    <div class="flex  flex-col">
                                        <InputLabel for="dt_type" value="DT Type" />
                                            <div class="flex ">
                                                <div class="flex items-center ml-2">
                                                    <label for="radio1" class="mr-2 text-sm font-medium text-gray-900">Public</label>
                                                    <input id="radio1" type="radio" name="dt_type" v-model="meterForm.dt_type" value="Public" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                                                </div>
                                                <div class="flex items-center ml-2">
                                                    <label for="radio2" class="mr-2 text-sm font-medium text-gray-900">Private</label>
                                                    <input id="radio2" type="radio" name="dt_type" v-model="meterForm.dt_type" value="Private" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                                                </div>      
                                                
                                            </div>  
                                            <InputError class="mt-2" :message="meterForm?.errors?.dt_type" />

                                        </div>
                              <!--  </div> -->


                             <!--  <div class="grid grid-cols-1  md:grid-cols-2 gap-2"> -->
                                        
                                    <div class="flex flex-col ">
                                        
                                            <BaseSelect v-model="meterForm.tariff" label="Present Tariff" :selected="meterForm.tariff"
                                                :options="tariffs"/>
                                                <InputError class="mt-2" :message="meterForm?.errors?.tariff" />  
                                    </div>

                                    <div class="flex flex-col ">
                                             <BaseSelect v-model="meterForm.advtariff" label="Advised Tariff" :selected="meterForm.advtariff"
                                                :options="tariffs"/>
                                                <InputError class="mt-2" :message="meterForm?.errors?.advtariff" />  
                                    </div>
                                    
                              </div>

                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-3 md:grid-cols-3">
                             <div class="flex flex-col ">
                                        <InputLabel for="fullname" value="Fullname" />
                                        <TextInput
                                            id="fullname"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="meterForm.fullname"
                                            placeholder="fullname"
                                            
                                        />
                                        <InputError class="mt-2" :message="meterForm?.errors?.fullname" />       
                                </div>
                             
                            
                             <div class="flex flex-col ">
                                        <InputLabel for="gsm" value="Phone Number" />
                                        <TextInput
                                            id="gsm"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="meterForm.gsm"
                                            placeholder="Phone Number"
                                            required
                                        />
                                        <InputError class="mt-2" :message="meterForm?.errors?.gsm" />       
                                </div>

                                <div class="flex flex-col ">
                                        <InputLabel for="email" value="Email" />
                                        <TextInput
                                            id="email"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="meterForm.email"
                                            placeholder="Customer Email"
                                            
                                        />
                                        <InputError class="mt-2" :message="meterForm?.errors?.email" />       
                                </div>
                        </div>





                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-3 md:grid-cols-3">
                             
                             <div class="flex flex-col ">
                                        <InputLabel for="feeder_33kv" value="33 kv Feeder" />
                                        <select v-model="meterForm.feeder_33kv" @change="load11kvFeeder($event.target.value)" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                            <option value="" selected>Choose 33 kv Feeder</option>
                                            <option v-for="option in feeder33s" :key="option.id" :value="option.id">
                                                {{ option.text }}
                                            </option>
                                        </select> 
                                      
                                        <InputError class="mt-2" :message="meterForm?.errors?.feeder_33kv" />       
                                </div>
                             <div class="flex flex-col ">
                                        <BaseSelect v-model="meterForm.feeder_11kv" label="11 kv Feeder" :selected="meterForm.feeder_11kv"
                                         :options="feeder11s"/>
                                        <InputError class="mt-2" :message="meterForm?.errors?.feeder_11kv" />       
                                </div>
                             <div class="flex flex-col ">
                                       
                                        <BaseSelect v-model="meterForm.premises" label="Use of Premises" :selected="meterForm.premises"
                                         :options="premises"/>
                                        <InputError class="mt-2" :message="meterForm?.errors?.premises" />       
                                </div>
                        </div>

                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 md:grid-cols-4">

                            <div class="flex  flex-col">
                                <InputLabel for="feeder_33kv" value="Customer Phase" />
                                    <div class="flex ">
                                        <div class="flex items-center ml-2">
                                            <label for="radio1" class="mr-2 text-sm font-medium text-gray-900">Red</label>
                                            <input id="radio1" type="radio" name="phase" v-model="meterForm.phase" value="Red" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                                        </div>
                                        <div class="flex items-center ml-2">
                                            <label for="radio2" class="mr-2 text-sm font-medium text-gray-900">Yellow</label>
                                            <input id="radio2" type="radio" name="phase" v-model="meterForm.phase" value="Yellow" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                                        </div>      
                                        <div class="flex items-center ml-2">
                                            <label for="radio3" class="mr-2 text-sm font-medium text-gray-900">Blue</label>
                                            <input id="radio3" type="radio" name="phase" v-model="meterForm.phase" value="Blue" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                                        </div>
                                    </div>  
                                        <InputError class="mt-2" :message="meterForm?.errors?.phase" />       

                                </div>
                             <div class="flex flex-col ">
                                        <BaseSelect v-model="meterForm.meter_type" label="Meter Type" :selected="meterForm.meter_type"
                                         :options="types"/>
                                        <InputError class="mt-2" :message="meterForm?.errors?.meter_type" />       
                                </div>
                             <div class="flex flex-col ">
                                       
                                        <BaseSelect v-model="meterForm.meter_brand" label="Meter Brand" :selected="meterForm.meter_brand"
                                         :options="brands"/>
                                        <InputError class="mt-2" :message="meterForm?.errors?.meter_brand" />       
                                </div>

                                 <div class="flex flex-col ">
                                        <InputLabel for="seal" value="Seal Number" />
                                        <TextInput
                                            id="seal"
                                            type="number"
                                            class="mt-1 block w-full"
                                            v-model="meterForm.seal"
                                            placeholder="New Seal Number"
                                            
                                        />
                                        <InputError class="mt-2" :message="meterForm?.errors?.seal" />       
                                </div>
                        </div>


                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 md:grid-cols-3">
                            
                                

                                

                                 <!--<div class="flex flex-col ">
                                        <InputLabel for="pole" value="Estimated Load" />
                                        <TextInput
                                            id="pole"
                                            type="number"
                                            class="mt-1 block w-full"
                                            v-model="meterForm.estimated"
                                            placeholder="Customer Estimated Load"
                                            
                                        />
                                        <InputError class="mt-2" :message="meterForm?.errors?.estimated" />       
                                </div> -->

                                 <div class="flex flex-col ">
                                        <InputLabel for="pole" value="Service Center" />
                                        <TextInput
                                            id="pole"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="meterForm.service_center"
                                            placeholder="Service Center"
                                            
                                        />
                                        <InputError class="mt-2" :message="meterForm?.errors?.service_center" />       
                                </div>

                                <div class="flex flex-col ">
                                        <InputLabel for="unit" value="Business Unit" />
                                        <TextInput
                                            id="unit"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="meterForm.business_unit"
                                            placeholder="Business Unit"
                                            
                                        />
                                        <InputError class="mt-2" :message="meterForm?.errors?.business_unit" />       
                                </div>

                                 <div class="flex flex-col ">
                                        <InputLabel for="account" value="Account Number" />
                                        <TextInput
                                            id="account"
                                            type="number"
                                            class="mt-1 block w-full"
                                            v-model="meterForm.account_no"
                                            placeholder="Customer Account Number"
                                            
                                        />
                                        <InputError class="mt-2" :message="meterForm?.errors?.account_no" />       
                                </div>

                        </div>

                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 md:grid-cols-4">
                            <div class="flex flex-col ">
                                        <InputLabel for="pole" value="Pole Number" />
                                        <TextInput
                                            id="pole"
                                            type="number"
                                            class="mt-1 block w-full"
                                            v-model="meterForm.pole"
                                            placeholder="e.g 11 "
                                            
                                        />
                                        <InputError class="mt-2" :message="meterForm?.errors?.pole" />       
                                </div>

                                <div class="flex flex-col ">
                                        <BaseSelect v-model="meterForm.installer" label="Installer" :selected="meterForm.installer"
                                         :options="installers"/>
                                        <InputError class="mt-2" :message="meterForm?.errors?.installer" />       
                                </div>

                                 <div class="flex flex-col ">
                                        <InputLabel for="latitude" value="Latitude" />
                                        <TextInput
                                            id="latitude"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="meterForm.x_cordinate"
                                            placeholder="0.1121223"
                                            
                                        />
                                        <InputError class="mt-2" :message="meterForm?.errors?.x_cordinate" />       
                                </div>
                                 <div class="flex flex-col ">
                                        <InputLabel for="longitude" value="Longitude" />
                                        <TextInput
                                            id="longitude"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="meterForm.y_cordinate"
                                            placeholder="0.01232333"
                                            
                                        />
                                        <InputError class="mt-2" :message="meterForm?.errors?.y_cordinate" />       
                                </div>


                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex flex-col">
                                <InputLabel for="email" value="Customer Address" />
                                <textarea class="staffForm-control border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"  v-model="meterForm.address" placeholder="Address"></textarea>
                                <InputError class="mt-2" :message="meterForm?.errors?.address" />
                            </div>
                            <div class="flex flex-col">
                                <InputLabel for="email" value="Customer Remark" />
                                <textarea class="staffForm-control border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"  v-model="meterForm.remark" placeholder="Address"></textarea>
                                <InputError class="mt-2" :message="meterForm?.errors?.remark" />
                            </div>

                        </div>
                        </div>

           </form>
        </Modal>
       <div class="text-right">
         <button @click="showModal = true " class="bg-optimal text-white p-1 mb-2 px-4 rounded text-right">Form</button> 
       </div>

        <div class="overflow-auto rounded-lg shadow">
            <TextInput
                                            id="longitude"
                                            type="text"
                                            class="mt-1 block w-full"
                                           @keyup="handleKeyup" 
                                            placeholder="enter account number or name"
                                            
                                        />
                <table class="w-full">
                    <thead class="bg-gray-50 border-b-2 border-gray-200">
                        <tr>
                            <th width ="5%" class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">S/N</th>
                            <!-- <th class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">Region</th> -->
                            <th class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">ALLOCATED METER NUMBER</th>
                            <th class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">ACCOUNT NUMBER</th>
                            <th class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">METER TYPE</th>
                           <!---<th class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">map</th> -->
                            <th class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">CUSTOMER NAME</th>
                            <th class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">ADDRESS</th>
                            <th class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">PHONE NO</th>
                            <th class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">FEEDER NAME</th>
                            <!--- <th class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">Business Unit</th>
                            <th class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">state</th>
                           <th class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">total_billings</th>
                            <th class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">total_settlement</th> -->
                            <th width ="5%"  class="p-3 text-sm font-semibold tracking-wide text-left table-bordered"> 
                                <font-awesome-icon class="fa-solid fas fa-cog"/>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="bg-white" v-for="(item,loop) in schedules" :key="loop">
                            <td class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">{{ loop+1 }}</td>
                           <!--<td class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">{{ item?.region?.region }}</td> -->
                            <td class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">{{ item?.allocated_meter_number }}</td>
                            <td class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">{{ item?.account_number }}</td>
                            <td class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">{{ item?.meter_type }}</td>
                           <!-- <td class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">{{ item?.map }}</td> -->
                            <td class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">{{ item?.customer_name }}</td>
                            <td class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">{{ item?.address }}</td>
                            <td class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">{{ item?.phone_no }}</td>
                            <td class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">{{ item?.feeder_name }}</td>
                            <!--<td class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">{{ item?.business_unit }}</td>
                            <td class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">{{ item?.state }}</td>
                            <td class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">{{ item?.total_billings }}</td>
                            <td class="p-3 text-sm font-semibold tracking-wide text-left table-bordered">{{ item?.total_settlement }}</td> -->
                           <td class="p-3 text-sm font-semibold tracking-wide text-left table-bordered" >
                                <button class="p-1 oy-1 text-sm bg-optimal text-white me-2 inline-block rounded" @click="showForm(item)">Record</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                
        </div>
    </div>
</template>


<style scoped>

</style>