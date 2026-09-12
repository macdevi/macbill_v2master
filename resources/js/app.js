import './bootstrap';

window.pppoeMonitor=function(){

return {

total:0,
online:0,
offline:0,

allUsers:[],
onlineUsers:[],
offlineUsers:[],

modal:false,
title:'',
detail:[],

async load(){

try{

let r=await fetch('/api/dashboard/pppoe');
let d=await r.json();

this.total=d.total ?? 0;
this.online=d.online ?? 0;
this.offline=d.offline ?? 0;

this.allUsers=d.users ?? [];
this.onlineUsers=d.online_users ?? [];
this.offlineUsers=d.offline_users ?? [];

}catch(e){

console.log(e);

}

setTimeout(()=>this.load(),10000);

},


show(type){

this.modal=true;


if(type==='total'){

this.title='Total PPPoE';
this.detail=this.allUsers;

}


if(type==='online'){

this.title='Pelanggan Online';
this.detail=this.onlineUsers;

}


if(type==='offline'){

this.title='Pelanggan Offline';
this.detail=this.offlineUsers;

}


}

}

}
