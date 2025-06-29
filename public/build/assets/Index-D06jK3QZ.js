import{d as b,c as u,o as n,g as t,u as e,p as M,w as s,a,j as f,S as _,h as i,F as h,k as F,i as y,t as m,W as C}from"./app-Izo5EmGm.js";import{_ as D}from"./AppLayout.vue_vue_type_script_setup_true_lang-ggOpj001.js";import{a as d,_ as c}from"./index-CMD7FrCV.js";import{_ as p,a as I,b as S,c as x}from"./CardTitle.vue_vue_type_script_setup_true_lang-DOOAIhtv.js";import{_ as $,F as g}from"./CardDescription.vue_vue_type_script_setup_true_lang-DvgxmvZy.js";import"./useForwardExpose-29XizuJ_.js";import"./RovingFocusGroup-B-my1ccE.js";/**
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
 */const k=d("PlusIcon",[["path",{d:"M5 12h14",key:"1ays0h"}],["path",{d:"M12 5v14",key:"s699le"}]]);/**
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
 */const B=d("UserIcon",[["path",{d:"M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2",key:"975kel"}],["circle",{cx:"12",cy:"7",r:"4",key:"17ys0d"}]]),H={class:"flex h-full flex-1 flex-col gap-4 rounded-xl p-4"},L={class:"flex items-center justify-between"},P={key:0,class:"space-y-4"},q={class:"flex items-center justify-between"},T={class:"flex items-center gap-1"},E={class:"text-xs"},U={class:"flex items-center gap-2"},W=["href","download"],Y={class:"flex items-center gap-2 text-sm text-muted-foreground"},ee=b({__name:"Index",props:{forms:{}},setup(A){const v=[{title:"Forms",href:"/forms"}],w=o=>{confirm("Are you sure you want to delete this form?")&&C.delete(route("forms.destroy",o))};return(o,l)=>(n(),u(h,null,[t(e(M),{title:"Forms"}),t(D,{breadcrumbs:v},{default:s(()=>[a("div",H,[a("div",L,[l[1]||(l[1]=a("div",{class:"flex flex-col space-y-2"},[a("h1",{class:"text-2xl font-semibold tracking-tight"},"Forms"),a("p",{class:"text-muted-foreground"},"Manage your form submissions.")],-1)),t(e(c),{"as-child":""},{default:s(()=>[t(e(_),{href:o.route("forms.create"),class:"flex items-center gap-2"},{default:s(()=>[t(e(k),{class:"h-4 w-4"}),l[0]||(l[0]=i(" Create Form "))]),_:1,__:[0]},8,["href"])]),_:1})]),o.forms.data.length>0?(n(),u("div",P,[(n(!0),u(h,null,F(o.forms.data,r=>(n(),f(e(p),{key:r.id},{default:s(()=>[t(e(I),null,{default:s(()=>[a("div",q,[a("div",null,[t(e(S),{class:"flex items-center gap-2"},{default:s(()=>[t(e(B),{class:"h-4 w-4"}),i(" "+m(r.name),1)]),_:2},1024),t(e($),{class:"flex items-center gap-4 mt-1"},{default:s(()=>[a("span",T,[t(e(j),{class:"h-3 w-3"}),i(" "+m(new Date(r.date).toLocaleDateString()),1)]),a("span",E," Submitted: "+m(new Date(r.created_at).toLocaleDateString()),1)]),_:2},1024)]),a("div",U,[t(e(c),{variant:"outline",size:"sm","as-child":""},{default:s(()=>[t(e(_),{href:o.route("forms.edit",r.id),class:"flex items-center gap-1"},{default:s(()=>[t(e(N),{class:"h-3 w-3"}),l[2]||(l[2]=i(" Edit "))]),_:2,__:[2]},1032,["href"])]),_:2},1024),r.file_url?(n(),f(e(c),{key:0,variant:"outline",size:"sm","as-child":""},{default:s(()=>[a("a",{href:r.file_url,download:r.file_original_name,class:"flex items-center gap-1"},[t(e(V),{class:"h-3 w-3"}),l[3]||(l[3]=i(" Download "))],8,W)]),_:2},1024)):y("",!0),t(e(c),{variant:"outline",size:"sm",onClick:G=>w(r.id),class:"text-destructive hover:text-destructive"},{default:s(()=>[t(e(z),{class:"h-3 w-3"})]),_:2},1032,["onClick"])])])]),_:2},1024),r.file_original_name?(n(),f(e(x),{key:0},{default:s(()=>[a("div",Y,[t(e(g),{class:"h-3 w-3"}),a("span",null,m(r.file_original_name),1)])]),_:2},1024)):y("",!0)]),_:2},1024))),128))])):(n(),f(e(p),{key:1},{default:s(()=>[t(e(x),{class:"flex flex-col items-center justify-center py-12"},{default:s(()=>[t(e(g),{class:"h-12 w-12 text-muted-foreground mb-4"}),l[5]||(l[5]=a("h3",{class:"text-lg font-medium mb-2"},"No forms yet",-1)),l[6]||(l[6]=a("p",{class:"text-muted-foreground text-center mb-4"}," You haven't created any forms yet. Get started by creating your first form. ",-1)),t(e(c),{"as-child":""},{default:s(()=>[t(e(_),{href:o.route("forms.create"),class:"flex items-center gap-2"},{default:s(()=>[t(e(k),{class:"h-4 w-4"}),l[4]||(l[4]=i(" Create Your First Form "))]),_:1,__:[4]},8,["href"])]),_:1})]),_:1,__:[5,6]})]),_:1}))])]),_:1})],64))}});export{ee as default};
