class AbsensiModel {

  final String tanggal;
  final String mapel;
  final String status;

  AbsensiModel({

    required this.tanggal,
    required this.mapel,
    required this.status,
  });

  factory AbsensiModel.fromJson(
    Map<String, dynamic> json,
  ) {

    return AbsensiModel(

      tanggal: json['tanggal'] ?? "",

      mapel: json['mapel'] ?? "",

      status: json['status'] ?? "",
    );
  }

  Map<String, dynamic> toJson() {

    return {

      "tanggal": tanggal,

      "mapel": mapel,

      "status": status,
    };
  }
}