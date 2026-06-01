class SiswaModel {

  final String nama;
  final String kelas;

  SiswaModel({

    required this.nama,
    required this.kelas,
  });

  factory SiswaModel.fromJson(
    Map<String, dynamic> json,
  ) {

    return SiswaModel(

      nama: json['nama'] ?? '',

      kelas: json['kelas'] ?? '',
    );
  }
}