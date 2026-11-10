import { httpPost } from './http.js';
import { publish } from './helpers.js';

const setPubSub = async (pb, params) => {
 const channelNames =  Object.keys(params.channel);
 if (params.method == 'connect') {
  for await (const channel of channelNames) {
   pb.collection(channel).subscribe('*', (data) => {
    publish(channel, data);
   });
  }
 }else if (params.method == 'disconnect') {
  for await (const channel of channelNames) {
   pb.collection(channel).unsubscribe();
  } 
 }
 return true;
}
export const connect = async (params) => {
 const pb = new PocketBase(params.url);
 const auth = {
  username: params.id,
  password: params.key
 }
 const isLogin = await httpPost(`${params.url}/api/auth/login`, auth);
 if (isLogin.code == 200) {
  let newParams = {...params, method: 'connect'};
  await setPubSub(pb, newParams);
  console.log('Connected to notifier system');
  return pb;
 }else {
  return false;
 }
}
export const sendMsg = async (pb, payload, resend = false) => {
 const allkeys = Object.keys(payload.data);
 const allvalues = Object.values(payload.data);
 const allpairs = allkeys.map((v, k) => {
  return `${v}="${allvalues[k]}"`;
 })
 allpairs.push('read=false');
 let row = false;
 try {
  row = await pb.collection(payload.channel).getFirstListItem(allpairs.join(' && '));
 } catch (err) {
  row = false;
 }
 if (row == false) {
  const rs = await pb.collection(payload.channel).create(payload.data);
 }else {
  if (resend) {
   const rs = await pb.collection(payload.channel).update(row.id, payload.data);
   return true;
  }
  return false;
 }
}
export const disconnect = async (pb, params) => {
 pb.authStore.clear();
 let newParams = {...params, method: 'disconnect'};
 await setPubSub(pb, newParams);
 console.log('disconnected from notifier system');
 return true;
}