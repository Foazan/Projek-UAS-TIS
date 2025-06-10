Terdapat 3 service utama yang berjalan pada app ini.

1. auth-service
Service yang berperan dalam mengelola registrasi dan login pengguna, serta menghasilkan JWT untuk autentikasi.
Fungsi Utama:
- register (Request $request)
  Berfungsi menerima data user seperti nama, email, dan password(disimpan dalam bentuk hash), melakukan validasi, dan menyimpannya ke database.
- login (Request $request)
  Berperan dalam mencocokan email dan password user, jika email dan password cocok maka akan membuat JWT dengan waktu kadaluwarsa 1 jam. JWT kemudian akan dikirim ke client untuk autentikasi selanjutnya.


2. api-gateway
Service yang bertindak sebagai perantara antara client dengan service lainnya, serta melakukan verifikasi JWT dari request client.
Fungsi utama:
- forwardArts(Request $request)
  Berperan untuk mengecek token JWT dan meneruskan request yang ada ke art-service jika token valid.
- createArt(Request $request)
  Melakukan pengecekan token JWT dan mengambil data gambar dari request dan menguploadnya ke Cloudinary. Setelah berhasil upload gambar, URL gambar akan diambil dan data karya akan diteruskan ke art-service dengan menambahkan user_id.
- forwardDeleteArt($id, Request $request)
  Melakukan pengcekan token JWT dan meneruskan request DELETE /arts/{id} ke art-service dengan menambahkan X-User-ID pada header.
- forwardGetArtsByUser($user_id)
  Berperan meneruskan permintaan GET /arts/user/{user_id} ke art-service.
- forwardGetArtById($id)
  Berperan meneruskan permintaan GET /arts/{id} ke art-service.


3. art-service
Service yang bertugas menyimpan dan mengelola data kerya seni.
Fungsi utama:
- index()
  Berperan mengambil semua data karya dengan endpoint GET /arts.
- store($id, Request $request)
  Berperan menerima data karya yang dikirim dari api-gateway dan menyimpannya ke dalam database.
- destroy($id, Request $request)
  Berperan menerima request untuk menghapus karya dan mengecek apakah user_id berasal dari header yang sama dengan user_id pemilik karya. Jika user_id sama, maka gambar akan dihapus dari Cloudinary dan dihapus dari database.
- getByUser($user_id)
  Berperan mengambil semua karya milik user tertentu.
- getById($id)
  Berperan mengambil data satu karya berdasarkan id nya.


Berikut link untuk repositori android app:
https://github.com/Foazan/OnlyArt_Microservices_Android
