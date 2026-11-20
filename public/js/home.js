(() => {
  const {criteria, url} = window.APP;
  const $ = id => document.getElementById(id), root = document.documentElement;
  const esc = s => String(s ?? "").replace(/[&<>"']/g, c => ({"&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;"}[c]));
  const rp = n => Number(n).toLocaleString("id-ID");
  const MIN_N = 10; // penilai minimal agar hasil dianggap andal
  let pins = [], tl, tiles = 0;

  const map = L.map("map", {attributionControl: false}).setView([-0.4958, 117.1572], 13);
  L.control.attribution({prefix: false}).addTo(map);
  const layer = L.layerGroup().addTo(map);

  const CARTO_KEY = "cb1_5co5_1_38226f5ffa8e73cfa6648f";

  function setTiles() {
    if (tl) map.removeLayer(tl);
    const gaya = root.dataset.theme == "dark" ? "dark_all" : "voyager";
    tl = L.tileLayer(`https://basemaps.cartocdn.com/rastertiles/${gaya}/{z}/{x}/{y}.png?key=${CARTO_KEY}`, {maxZoom: 19, attribution: "© OpenStreetMap, © CARTO"}).addTo(map);
    tl.on("tileload", () => tiles++);
  }
  setTiles();
  window.addEventListener("themechange", () => { if (tiles) setTiles(); });
  setTimeout(() => {
    if (tiles) return;
    tl.setUrl("https://tile.openstreetmap.org/{z}/{x}/{y}.png");
    setTimeout(() => { if (!tiles) $("mapnote").textContent = "Gambar peta dasar belum termuat. Marker rental tetap tampil dan tombol Rute membuka Google Maps."; }, 5000);
  }, 5000);

  $("ws").innerHTML = Object.entries(criteria).map(([k, l]) =>
    `<label>${esc(l)}<input type="range" min="0" max="5" value="3" data-k="${k}"><output>3</output></label>`).join("");
  let timer;
  document.querySelectorAll("[data-k]").forEach(e => e.oninput = () => {
    e.nextElementSibling.textContent = e.value;
    clearTimeout(timer);
    timer = setTimeout(() => load(false), 150);
  });

  async function load(fit) {
    const q = new URLSearchParams();
    document.querySelectorAll("[data-k]").forEach(e => q.set(`w[${e.dataset.k}]`, e.value));
    try {
      const r = await fetch(url + "?" + q);
      if (!r.ok) throw 0;
      draw(await r.json(), fit);
    } catch (e) {
      $("info").textContent = "Gagal memuat data dari server.";
    }
  }

  function draw(d, fit) {
    layer.clearLayers(); pins = [];
    const top = d.items[0];
    $("info").innerHTML = `<b>${d.total} penilaian</b> masuk` + (top && top.v != null ? `. Peringkat 1: <b>${esc(top.nama)}</b>` : ". Belum ada penilaian, isi survei dulu.");
    $("list").innerHTML = d.items.map((a, i) => {
      const rute = `https://www.google.com/maps/dir/?api=1&destination=${a.lat},${a.lng}`;
      const m = L.marker([a.lat, a.lng], {icon: L.divIcon({className: "", html: `<div class="pin"><span>${i + 1}</span></div>`, iconSize: [28, 28], iconAnchor: [14, 26]})})
        .bindPopup(`<b>${esc(a.nama)}</b><br>${esc(a.alamat)}<br>Rp${rp(a.harga)}/jam<br><a target="_blank" rel="noopener" href="${rute}">Rute di Google Maps</a>`).addTo(layer);
      pins.push(m);
      const wa = a.wa ? `<a target="_blank" rel="noopener" href="https://wa.me/${esc(String(a.wa).replace(/^0/, "62"))}">WhatsApp</a>` : "";
      return `<article class="card" data-i="${i}"><div class="rk">${i + 1}</div><div class="bd"><h3>${esc(a.nama)}</h3>
        <p class="mu">${esc(a.kecamatan)}, ${esc(a.alamat)}</p>
        <div class="meta"><span><b>Rp${rp(a.harga)}</b>/jam</span><span>Buka ${esc(a.jam)}</span><span>${a.n} penilai</span>${a.puas != null ? `<span>Kepuasan ${a.puas} dari 5</span>` : ""}</div>
        ${a.n < MIN_N ? `<div class="warn">${a.n ? `Baru ${a.n} penilai, hasil belum andal (minimal ${MIN_N})` : "Belum ada penilai"}</div>` : ""}
        <div class="bar"><u style="width:${a.v != null ? (a.v * 100).toFixed(0) : 0}%"></u></div>
        <div class="act"><small>Skor SAW ${a.v != null ? Number(a.v).toFixed(3) : "-"}</small>${wa}<a target="_blank" rel="noopener" href="${rute}">Rute</a></div></div></article>`;
    }).join("");
    $("tbl").innerHTML = `<table><tr><th>Rental</th>${Object.values(criteria).map(l => `<th>${esc(l)}</th>`).join("")}</tr>` +
      d.items.map(a => `<tr><td>${esc(a.nama)}</td>${Object.keys(criteria).map(k => {
        const v = a.x[k];
        return `<td style="background:${v != null ? `color-mix(in srgb,var(--ok) ${Math.max(0, (v - 1) / 4 * 70)}%,transparent)` : "transparent"}">${v != null ? Number(v).toFixed(2) : "-"}</td>`;
      }).join("")}</tr>`).join("") + `</table>`;
    if (fit) map.fitBounds(L.featureGroup(pins).getBounds().pad(.3), {maxZoom: 14});
  }

  $("list").onclick = e => {
    const c = e.target.closest(".card");
    if (!c || e.target.closest("a")) return;
    const m = pins[+c.dataset.i];
    map.flyTo(m.getLatLng(), 16); m.openPopup();
    if (innerWidth <= 800) scrollTo({top: 0, behavior: "smooth"});
  };

  load(true);
})();