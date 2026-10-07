import { createStore } from "vuex";
import axios from 'axios'
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const store = createStore({
    state:{
        user:{
            token: localStorage.getItem('TVATI_TOKEN') == 'null' ? null :localStorage.getItem('TVATI_TOKEN'),
            data: localStorage.getItem('TVATI_USERDATA') ? JSON.parse(localStorage.getItem('TVATI_USERDATA')) : 'null' ,
            roles: localStorage.getItem('TVATI_USER_ROLES') ? JSON.parse(localStorage.getItem('TVATI_USER_ROLES')) : 'null'
        },
        activeRole: localStorage.getItem('TVATI_ACTIVE_ROLE'),
        approvalLevel: localStorage.getItem('TVATI_APPROVAL_LEVEL'),
        spinnerLoader:false,
        notification: {
            status: false,
            message: 'success',
            type: 'success'
        },


        loadingProcess:false,
        units:['pieces', 'measurement' , 'carton' , 'packet'],
        memoStatus:['','General','Selected Departments','Selected Staff'],
        // memoStatus:['','General','Selected Departments','Selected Staff'],
    },
    actions:{

        // end of auth

            //load staff
            getMethod({commit},{url}){
                commit('setSpinner', true)
                return axios.get(url)
                    .then(({data})=>{
                        if(data?.status == 205){
                            commit('notify',{message:data?.message,type:'danger'})
                        }

                        commit('setSpinner', false)
                        return data;
                    }).catch(e => {
                        commit('setSpinner', false)
                        console.log(e);
                        commit('notify', { message: e?.code, type: 'danger' })

                        if(e?.code == '"ERR_NETWORK"'){
                            store.commit('notify', { message: e?.message, type: 'danger' })
                        }
                        // alert('weting be this')
                    })
            },
            //load staff
            postMethod({commit},{url,param,headers={}}){
                commit('setSpinner', true)
                return axios.post(url,param,{
                     headers: headers,
                })
                    .then(({data})=>{
                        if(data?.status == 201){
                            commit('notify',{message:data.message})
                        }
                        else if(data?.status == 422){
                            commit('notify',{message:data.message,type:'warning'})
                        }else{
                            commit('notify',{message:data.message,type:'danger'})
                        }
                        commit('setSpinner', false)
                        return data;
                    }).catch(e => {
                        commit('setSpinner', false)
                        console.log(e);
                        const responseData = e?.response?.data
                        const status = responseData?.status ?? e?.response?.status

                        commit('notify', {
                            message: responseData?.message ?? e?.message ?? 'Request failed',
                            type: status === 422 ? 'warning' : 'danger'
                        })

                        if (responseData) {
                            return {
                                ...responseData,
                                status,
                                data: responseData.data ?? responseData.errors ?? []
                            }
                        }
                    })
            },

            deleteMethod({commit},{url,prompt = 'are you sure, you want to delete this ?', param = null}){
                if(prompt != null){
                    if(!confirm(prompt)){
                        return false
                    }
                }
                 commit('setSpinner', true)
                return axios.delete(url,param)
                    .then(({data})=>{
                        if(data?.status == 201){
                            commit('notify',{message:data.message})
                        }
                       else{
                            commit('notify',{message:data.message,type:'danger'})
                        }
                        commit('setSpinner', false)
                        return data;
                    }).catch(e => {
                        if(e?.code == '"ERR_NETWORK"'){
                            store.commit('notify', { message: e?.message, type: 'danger' })
                        }
                        commit('setSpinner', false)

                        console.log(e);
                    })

            },

            putMethod({commit},{url,prompt = null, param = null}){
                if(prompt != null){
                    if(!confirm(prompt)){
                        return false
                    }
                }
                commit('setSpinner', true)
                param._token = page.props.csrf_token
                return axios.put(url,param)
                    .then(({data})=>{
                        if(data?.status == 201){
                            commit('notify',{message:data.message})
                        }
                       else{
                            commit('notify',{message:data.message,type:'danger'})
                        }
                        commit('setSpinner', false)
                        return data;
                    }).catch(e => {

                        if(e?.code == '"ERR_NETWORK"'){
                            store.commit('notify', { message: e?.message, type: 'danger' })
                        }
                        commit('setSpinner', false)

                        console.log(e);
                    })
            },

            // load dropdown
            loadDropdown({},url){
            return axios.get('/drop-'+url)
                    .then(({data})=>{
                        if(data?.status==200){
                            return data;
                        }else{
                            return [];
                        }
                    })

        },


    },
    mutations:{
        setSpinner: (state,spin) =>{
            state.spinnerLoader = spin;
        },
        notify: (state, { message, type='success' }) => {
            state.notification.status = true;
            state.notification.type = type;
            state.notification.message = message;
            setTimeout(() => {
                state.notification.status = false;
            }, 5000)
        },


    },
    getters:{
         // global functions
        numberFormat(number, point = 2) {
            return number.toFixed(point).replace(/\d(?=(\d{3})+\.)/g, '$&,')
        }
    },
    modules:{}
});

export default store;
