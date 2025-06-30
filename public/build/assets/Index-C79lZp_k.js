import{d as M,c as u,o as n,g as t,u as e,p as F,w as s,a,j as f,S as _,h as i,F as h,k as C,i as y,t as m,W as p}from"./app-CpWbJJER.js";import{_ as D}from"./AppLayout.vue_vue_type_script_setup_true_lang-UWRjaSCJ.js";import{a as d,_ as c}from"./AppLogoIcon.vue_vue_type_script_setup_true_lang-Cn4VzyVq.js";import{_ as x,a as S,b as $,c as g}from"./CardTitle.vue_vue_type_script_setup_true_lang-DNeIjIFJ.js";import{_ as I,F as k}from"./CardDescription.vue_vue_type_script_setup_true_lang-DyGu-_SZ.js";import"./useForwardExpose-BI4lYtN6.js";import"./RovingFocusGroup-D0-6ITzG.js";/**
 * @license lucide-vue-next v0.468.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const j=d("CalendarIcon",[["path",{d:"M8 2v4",key:"1cmpym"}],["path",{d:"M16 2v4",key:"4m81vk"}],["rect",{width:"18",height:"18",x:"3",y:"4",rx:"2",key:"1hopcy"}],["path",{d:"M3 10h18",key:"8toen8"}]]);/**
 * @license lucide-vue-next v0.468.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const V=d("DownloadIcon",[["path",{d:"M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4",key:"ih7n3h"}],["polyline",{points:"7 10 12 15 17 10",key:"2ggqvy"}],["line",{x1:"12",x2:"12",y1:"15",y2:"3",key:"1vk2je"}]]);/**
 * @license lucide-vue-next v0.468.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const v=d("PlusIcon",[["path",{d:"M5 12h14",key:"1ays0h"}],["path",{d:"M12 5v14",key:"s699le"}]]);/**
 * @license lucide-vue-next v0.468.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const N=d("SquarePenIcon",[["path",{d:"M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7",key:"1m0v6g"}],["path",{d:"M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z",key:"ohrbg2"}]]);/**
 * @license lucide-vue-next v0.468.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const z=d("Trash2Icon",[["path",{d:"M3 6h18",key:"d0wm0j"}],["path",{d:"M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6",key:"4alrt4"}],["path",{d:"M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2",key:"v07s0e"}],["line",{x1:"10",x2:"10",y1:"11",y2:"17",key:"1uufr5"}],["line",{x1:"14",x2:"14",y1:"11",y2:"17",key:"xtxkd"}]]);/**
 * @license lucide-vue-next v0.468.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const B=d("UserIcon",[["path",{d:"M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2",key:"975kel"}],["circle",{cx:"12",cy:"7",r:"4",key:"17ys0d"}]]),H={class:"flex h-full flex-1 flex-col gap-4 rounded-xl p-4"},L={class:"flex items-center justify-between"},P={key:0,class:"space-y-4"},q={class:"flex items-center justify-between"},T={class:"flex items-center gap-1"},E={class:"text-xs"},U={class:"flex items-center gap-2"},W=["href","download"],Y={class:"flex items-center gap-2 text-sm text-muted-foreground"},ee=M({__name:"Index",props:{forms:{}},setup(A){const w=[{title:"Forms",href:"/forms"}],b=r=>{confirm("Are you sure you want to delete this form?")&&(p.delete(route("forms.destroy",r)),p.delete(route("clocking.destroy",r)))};return(r,l)=>(n(),u(h,null,[t(e(F),{title:"Forms"}),t(D,{breadcrumbs:w},{default:s(()=>[a("div",H,[a("div",L,[l[1]||(l[1]=a("div",{class:"flex flex-col space-y-2"},[a("h1",{class:"text-2xl font-semibold tracking-tight"},"Forms"),a("p",{class:"text-muted-foreground"},"Manage your form submissions.")],-1)),t(e(c),{"as-child":""},{default:s(()=>[t(e(_),{href:r.route("forms.create"),class:"flex items-center gap-2"},{default:s(()=>[t(e(v),{class:"h-4 w-4"}),l[0]||(l[0]=i(" Create Form "))]),_:1,__:[0]},8,["href"])]),_:1})]),r.forms.data.length>0?(n(),u("div",P,[(n(!0),u(h,null,C(r.forms.data,o=>(n(),f(e(x),{key:o.id},{default:s(()=>[t(e(S),null,{default:s(()=>[a("div",q,[a("div",null,[t(e($),{class:"flex items-center gap-2"},{default:s(()=>[t(e(B),{class:"h-4 w-4"}),i(" "+m(o.name),1)]),_:2},1024),t(e(I),{class:"flex items-center gap-4 mt-1"},{default:s(()=>[a("span",T,[t(e(j),{class:"h-3 w-3"}),i(" "+m(new Date(o.date).toLocaleDateString()),1)]),a("span",E," Submitted: "+m(new Date(o.created_at).toLocaleDateString()),1)]),_:2},1024)]),a("div",U,[t(e(c),{variant:"outline",size:"sm","as-child":""},{default:s(()=>[t(e(_),{href:r.route("forms.edit",o.id),class:"flex items-center gap-1"},{default:s(()=>[t(e(N),{class:"h-3 w-3"}),l[2]||(l[2]=i(" Edit "))]),_:2,__:[2]},1032,["href"])]),_:2},1024),o.file_url?(n(),f(e(c),{key:0,variant:"outline",size:"sm","as-child":""},{default:s(()=>[a("a",{href:o.file_url,download:o.file_original_name,class:"flex items-center gap-1"},[t(e(V),{class:"h-3 w-3"}),l[3]||(l[3]=i(" Download "))],8,W)]),_:2},1024)):y("",!0),t(e(c),{variant:"outline",size:"sm",onClick:G=>b(o.id),class:"text-destructive hover:text-destructive"},{default:s(()=>[t(e(z),{class:"h-3 w-3"})]),_:2},1032,["onClick"])])])]),_:2},1024),o.file_original_name?(n(),f(e(g),{key:0},{default:s(()=>[a("div",Y,[t(e(k),{class:"h-3 w-3"}),a("span",null,m(o.file_original_name),1)])]),_:2},1024)):y("",!0)]),_:2},1024))),128))])):(n(),f(e(x),{key:1},{default:s(()=>[t(e(g),{class:"flex flex-col items-center justify-center py-12"},{default:s(()=>[t(e(k),{class:"h-12 w-12 text-muted-foreground mb-4"}),l[5]||(l[5]=a("h3",{class:"text-lg font-medium mb-2"},"No forms yet",-1)),l[6]||(l[6]=a("p",{class:"text-muted-foreground text-center mb-4"}," You haven't created any forms yet. Get started by creating your first form. ",-1)),t(e(c),{"as-child":""},{default:s(()=>[t(e(_),{href:r.route("forms.create"),class:"flex items-center gap-2"},{default:s(()=>[t(e(v),{class:"h-4 w-4"}),l[4]||(l[4]=i(" Create Your First Form "))]),_:1,__:[4]},8,["href"])]),_:1})]),_:1,__:[5,6]})]),_:1}))])]),_:1})],64))}});export{ee as default};
