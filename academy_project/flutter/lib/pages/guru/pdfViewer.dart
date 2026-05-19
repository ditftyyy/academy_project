import 'package:flutter/material.dart';

import 'package:syncfusion_flutter_pdfviewer/pdfviewer.dart';

class PdfViewerPage
    extends StatelessWidget {

  final String title;

  final String assetPath;

  const PdfViewerPage({

    super.key,

    required this.title,

    required this.assetPath,
  });

  @override
  Widget build(BuildContext context) {

    return Scaffold(

      appBar: AppBar(

        title: Text(title),

        backgroundColor:
            Colors.red,
      ),

      body: SfPdfViewer.asset(
        assetPath,
      ),
    );
  }
}