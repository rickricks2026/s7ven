const paths={
'arrow-up-right':'<path d="M7 17 17 7M7 7h10v10"/>','arrow-left':'<path d="m12 5-7 7 7 7M5 12h14"/>',
user:'<circle cx="12" cy="8" r="4"/><path d="M4 21v-2a8 8 0 0 1 16 0v2"/>',menu:'<path d="M4 6h16M4 12h16M4 18h16"/>',x:'<path d="m6 6 12 12M6 18 18 6"/>',
lock:'<rect x="5" y="10" width="14" height="11" rx="2"/><path d="M8 10V6a4 4 0 0 1 8 0v4M12 14v3"/>',
sparkles:'<path d="m12 3 2.8 6.2L21 12l-6.2 2.8L12 21l-2.8-6.2L3 12l6.2-2.8L12 3ZM20 2v4M18 4h4"/>',
layers:'<path d="m12 3 10 5-10 5L2 8l10-5ZM2 12l10 5 10-5M2 16l10 5 10-5"/>',
play:'<path d="m9 5 11 7-11 7V5Z"/>',more:'<circle cx="5" cy="12" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/>',
code:'<path d="m8 6-6 6 6 6m8-12 6 6-6 6M14 3l-4 18"/>',shield:'<path d="M12 2 3 6v6c0 6 9 10 9 10s9-4 9-10V6l-9-4Z"/><path d="m8 12 3 3 5-6"/>',
zap:'<path d="m13 2-10 12h8l-1 8 11-13h-8l0-7Z"/>',headphones:'<path d="M3 14v-3a9 9 0 0 1 18 0v3"/><rect x="2" y="12" width="5" height="9" rx="2"/><rect x="17" y="12" width="5" height="9" rx="2"/>',
box:'<path d="m12 2 10 5v10l-10 5-10-5V7l10-5ZM2 7l10 5 10-5M12 12v10M7 4.5l10 5V14"/>',sliders:'<path d="M4 3v6m0 6v6M12 3v12m0 4v2M20 3v2m0 6v10"/><circle cx="4" cy="12" r="3"/><circle cx="12" cy="16" r="3"/><circle cx="20" cy="8" r="3"/>',
plus:'<path d="M12 5v14M5 12h14"/>',message:'<path d="M21 11.5a8.4 8.4 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7a8.4 8.4 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.4 8.4 0 0 1 3.8-.9h.5a8.5 8.5 0 0 1 8 8v.5Z"/>',
eye:'<path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/>',search:'<circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 5 5"/>',grid:'<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>',
key:'<circle cx="8" cy="8" r="5"/><path d="m12 12 9 9M17 17l3-3M14 14l3-3"/>','log-out':'<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4m7 14 5-5-5-5M21 12H9"/>',file:'<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6ZM14 2v6h6M8 13h8M8 17h5"/>',
loader:'<path d="M21 12a9 9 0 1 1-6.2-8.6"/>',globe:'<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c5 5 5 13 0 18-5-5-5-13 0-18Z"/>',rocket:'<path d="M14 4c3-2 7-2 7-2s0 4-2 7L10 18l-4-4 8-10ZM6 14H2l4-6h5M10 18v4l6-4v-5M5 19l-3 3"/><circle cx="16" cy="7" r="1.5"/>','shopping-bag':'<path d="m5 7-2 14h18L19 7H5ZM8 8V6a4 4 0 0 1 8 0v2"/>',check:'<path d="m5 12 4 4L19 6"/>'};
export function icon(name){return `<svg viewBox="0 0 24 24" aria-hidden="true">${paths[name]||paths.layers}</svg>`}
export function hydrate(root=document){root.querySelectorAll('i[data-icon]').forEach(el=>{if(!el.querySelector('svg'))el.innerHTML=icon(el.dataset.icon)})}
