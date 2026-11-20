@extends('layouts.app')

@push('head')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
@endpush

@section('content')
<main>
  <section id="side">
    <div id="info"></div>
    <div id="mapnote" class="note"></div>
    <div id="list"></div>
    <details class="box"><summary>Atur bobot kriteria SAW</summary><div id="ws"></div></details>
    <div class="box">
      <b>Rata-rata nilai per kriteria</b>
      <p class="note">Skala 1 sampai 5. Makin hijau makin tinggi. Ini matriks keputusan (X) pada SAW.</p>
      <div class="tw" id="tbl"></div>
    </div>
    <p class="note">Skor SAW: nilai tiap kriteria dibagi nilai tertinggi di antara kelima rental (normalisasi), dikali bobot, lalu dijumlahkan. Makin mendekati 1, makin unggul. Perhitungan dilakukan di server (SawService).</p>
  </section>
  <div id="map" role="application" aria-label="Peta rental PS"></div>
</main>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
<script>window.APP = {criteria: @json($criteria), url: @json(route('ranking'))};</script>
<script src="{{ asset('js/home.js') }}"></script>
@endpush
