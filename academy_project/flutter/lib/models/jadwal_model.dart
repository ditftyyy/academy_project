class JadwalModel {

  final String hari;
  final String jam;
  final String mapel;
  final String ruangan;

  JadwalModel({

    required this.hari,
    required this.jam,
    required this.mapel,
    required this.ruangan,
  });

  factory JadwalModel.fromJson(
    Map<String, dynamic> json,
  ) {

    return JadwalModel(

      hari: json['hari'],
      jam: json['jam'],
      mapel: json['mapel'],
      ruangan: json['ruangan'],
    );
  }
}