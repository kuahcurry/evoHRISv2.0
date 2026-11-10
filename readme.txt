Update :
Frontend :
1. Semua module yang menggunakan EvoDialog sudah saya update menjadi memanggil showDialog global

Upgrade:
Frontend :
1. Penggunaan komponen EvoDialog tidak perlu import disetiap module, cukup panggil langsung function global showDialog 
   contoh minimal parameter : 
   showDialog({type: 'info', label: '<span class="text-black">Simpan sukses</span>'})

2. Event komponen EvoPopup onClosed otomatis membawa id dari module yang menjadi konten dari popup tersebut.
   contoh cara akses nya :
   mainPopup.on('onOpened', async (e) => {
    if (e.detail.children.id == 'tanda_tangan') {
     document.tanda_tangan.toolbar('load');
    }else if (e.detail.children.id == 'setting') {
     document.setting.loadData();
    }
   });

//... Alfan
3. setting features/rawat_jalan/sidebar-menu (update modules otomatis by master->modules)
4. setting features/rekam_medis/listpasien (setting colorcode antrian pasien)

Backend :
1. Folder vendor library2 yang hampir dipastikan tidak akan pernah dipake saya buang. (ukuran folder API berkurang +-40%);
2. Controller histori_pasien dan Model nya pindah ke root sub folder Controller (sub folder controller/histori_pasien sudah saya hapus);
3. Sub Folder cetakan di Controller saya pindah sesuai dengan controller class (sub folder controller/cetakan sudah saya hapus); 