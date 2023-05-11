<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePage  } from '@inertiajs/vue3'


import {onMounted, reactive, ref,computed} from "vue";
onMounted(()=>{
fetch('http://localhost:8000/users').then((res) => res.json())
    .then((json) => (users.value=[...json.filter(item=>item.id != account["id"])]))
    .catch((err) => (error.value = err))




})

async function  handleSaveMessage(){


  await  fetch(`http://localhost:8000/api/chat/storeMessage/${activeConversation.value}`,{
        method:'POST',
        body: JSON.stringify({ "message": message.value }),
      headers: { 'Content-Type': 'application/json' },
    })
}

function  handleClick(id){

    //listen on send event
    Echo.private(`sendMessagePrivate.${account['id']}.${id}`)
        .listen('SendMessage',e=>{
            allMessages.value.push({id:Math.random(),message:e.message,sender_id:account['id']
                ,receiver_id:destUser.user['id']})
            needRefresh.value=!needRefresh.value
            console.log(allMessages.value)
        })
    //listen on received event
    Echo.private(`receiveMessagePrivate.${id}.${account['id']}`)
        .listen('ReceiveMessage',e=>{
            allMessages.value.push({id:Math.random(),message:e.message,sender_id:destUser.user['id']
                ,receiver_id:account['id']})
            needRefresh.value=!needRefresh.value
            console.log(allMessages.value)
        })



    activeConversation.value=id
    fetch(`api/chat/user/${id}`).then(async res=> {
        destUser.user=await res.json()
    })

    fetch(`api/chat/allMessages/${id}`).then(async  res=> {
        allMessages.value=await res.json()
        console.log(allMessages.value)
    })


}
const destUser=reactive({user:null});
const activeConversation=ref('')
const allMessages=ref([]);
const account=reactive(usePage().props.auth.user);
const users=ref()
const needRefresh=ref(true)
const message=ref("")
console.log(users.value)
//
Echo.listen(`all_users`,'AvailableUser',(e) => {
    users.value=e[0].filter(item=>item.id != account["id"]);

    console.log(users.value)
    });



// Echo.join(`group_chat.1`)
//     .here((users) => {
//         console.log(users)
//     })
//     .joining((user) => {
//         console.log(user.name);
//     })
//     .leaving((user) => {
//         console.log(user.name);
//     })
//     .listen("GroupChatMessage",e=>{
//         console.log(e.message)
//     })
//     .error((error) => {
//         console.error(error);
//     });

console.log(destUser)
</script>

<template>

    <AppLayout title="Chat Room">
        <!-- component -->
        <div class="flex h-[97vh] antialiased text-gray-800">
            <div class="flex flex-row h-full w-full overflow-x-hidden">
                <div class="flex flex-col py-8 pl-6 pr-2 w-64 bg-white flex-shrink-0">
                    <div class="flex flex-row items-center justify-center h-12 w-full">
                        <div
                            class="flex items-center justify-center rounded-2xl text-indigo-700 bg-indigo-100 h-10 w-10"
                        >

                            <img v-if="account['profile_photo_path']" class="rounded-full" :src="'storage/'+account['profile_photo_path']"/>
                            <img v-else class="rounded-full" :src="account['profile_photo_url']"/>
                        </div>
                        <div class="ml-2 font-bold text-2xl">{{account['name']}}</div>
                    </div>

                    <div class="flex flex-col h-full mt-8 ">
                        <div class="flex flex-row items-center justify-between text-xs">
                            <span class="font-bold">Conversations</span>
                            <span
                                class="flex items-center justify-center bg-gray-300 h-4 w-4 rounded-full"
                            >{{users?.length}}</span
                            >
                        </div>
                        <div class="flex flex-col  space-y-1 mt-4 -mx-2 h-[400px] overflow-auto " >
                            <button
                                class="flex flex-row items-center hover:bg-gray-100 rounded-xl p-2" v-for="user in users"
                                @click="handleClick(user.id)"
                                :class="parseInt(activeConversation)===parseInt(user.id)? 'bg-gray-100 sticky top-0' :'' "
                            >


                                <div
                                    class="flex items-center justify-center  h-8 w-8 bg-indigo-200 rounded-full"
                                >
                                    <img  class="text-green bg-green-500 font-[2px] w-3 " src="https://img.icons8.com/?size=512&id=474&format=png" alt="">
                                    <img v-if="user['profile_photo_path']" class="rounded-full" :src="'storage/'+user['profile_photo_path']">
                                    <img v-else :src="user['profile_photo_url']">
                                </div>
                                <div class="ml-2 text-sm font-semibold" >{{user.name}}</div>
                            </button>
                        </div>

                    </div>
                </div>
                <div class="flex flex-col flex-auto h-full p-6 ">
                    <div
                        class="flex flex-col flex-auto flex-shrink-0 rounded-2xl bg-gray-100 h-full p-4"
                    >
                        <div class="flex flex-col h-fit overflow-x-auto ">
                            <div class="flex flex-col h-full">
                                <div v-if="!activeConversation" class="flex justify-center"> please select a conversation</div>
                                <div v-else class="grid grid-cols-12 gap-y-2" v-for="message in allMessages">

                                    <div class="col-start-1 col-end-8 p-3 rounded-lg"  v-if="message.receiver_id===account['id']">

                                        <div class="flex flex-row items-center">
                                            <div
                                                class="flex items-center justify-center h-10 w-10 rounded-full bg-indigo-500 flex-shrink-0"
                                            >
                                                <img v-if="destUser?.user['profile_photo_path']" class="rounded-full" :src="'storage/'+destUser.user['profile_photo_path']">
                                                <img v-else class="rounded-full" :src="destUser.user['profile_photo_url']">

                                            </div>
                                            <div
                                                class="relative ml-3 text-sm bg-white py-2 px-4 shadow rounded-xl"
                                            >
                                                <div>{{message.message}}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="activeConversation"  class="col-start-6 col-end-13 p-3 rounded-lg">
                                        <div class="flex items-center justify-start flex-row-reverse">
                                            <div
                                                class="flex items-center justify-center h-10 w-10 rounded-full bg-indigo-500 flex-shrink-0"
                                            >
                                                <img v-if="account['profile_photo_path']" class="rounded-full" :src="'storage/'+account['profile_photo_path']">
                                                <img v-else class="rounded-full" :src="account['profile_photo_url']">

                                            </div>
                                            <div
                                                class="relative mr-3 text-sm bg-indigo-100 py-2 px-4 shadow rounded-xl"
                                            >
                                                <div>
                                                   {{message.message}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>





                                </div>
                            </div>
                        </div>
                        <div
                            class="flex flex-row items-center h-16  rounded-xl bg-white w-full px-4 absolute bottom-0"
                        style="width: 78%"
                        >
                            <div>
                                <button
                                    class="flex items-center justify-center text-gray-400 hover:text-gray-600"
                                >
                                    <svg
                                        class="w-5 h-5"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"
                                        ></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="flex-grow ml-4 ">
                                <div class="relative w-full">

                                    <input
                                        type="text"
                                        v-model="message"

                                        class="flex w-full border rounded-xl focus:outline-none focus:border-indigo-300 pl-4 h-10"
                                    />

                                    <button
                                        class="absolute flex items-center justify-center h-full w-12 right-0 top-0 text-gray-400 hover:text-gray-600"

                                    >
                                        <svg
                                            class="w-6 h-6"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                            ></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="ml-4">
                                <button
                                    @click="handleSaveMessage()"
                                    class="flex items-center justify-center bg-indigo-500 hover:bg-indigo-600 rounded-xl text-white px-4 py-1 flex-shrink-0"
                                >
                                    <span>Send</span>
                                    <span class="ml-2">
                  <svg
                      class="w-4 h-4 transform rotate-45 -mt-px"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                      xmlns="http://www.w3.org/2000/svg"
                  >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"
                    ></path>
                  </svg>
                </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
