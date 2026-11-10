import { httpPost } from './http.js';

let pb = null;
let events = {};

export const connect = async (params) => {
 pb = new PocketBase(params.url);
 const auth = {
  username: params.id,
  password: params.key
 }
 const isLogin = await httpPost(`${params.url}/api/auth/login`, auth);
 if (isLogin.code == 200) {
  console.info('Connected to notifier system');
  return pb;
 }else {
  return false;
 }
}
export const disconnect = async (pb) => {
 pb.authStore.clear();
 console.info('disconnected from notifier system');
 pb = null;
 events = [];
 return true;
}
export const subscribe = (chnName, fn, scope = 'internal') => {
 if (config.trace_info) console.info(`someone subscribe to ${chnName}`);
 if (scope == 'external') pb.collection(chnName).subscribe('*', data => fn(data));
 events[chnName] = events[chnName] || [];
 events[chnName].push(fn);
 return true;
}
export const publish = async (chnName, data, resend = false, scope = 'internal') => {
 if (scope == 'external') {
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
  return true;
 }
 if (events[chnName]) {
  if (config.trace_info) console.info(`someone publish ${chnName}`);
  events[chnName].forEach(f => {
   f(data);
  });
  return true;
 } 
}
export const unsubscribe = (chnName, fn, scope = 'internal') => {
 if (chnName == '*') {
  if (config.trace_info) console.info(`everyone unsubscribe from ${chnName}`);
  if (scope == 'external') Object.keys(events).forEach(f => pb.collection(f).unsubscribe());
  events = [];
  return true;
 }
 if (events[chnName]) {
  if (config.trace_info) console.info(`someone unsubscribe from ${chnName}`);
  if (scope == 'external') pb.collection(chnName).unsubscribe();
  events[chnName] = events[chnName].filter(f => f !== fn);
  if (events[chnName].length == 0) delete events[chnName];
  return true;
 } 
}
export const getAllEvents = () => {
 return events;
}