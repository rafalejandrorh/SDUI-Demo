import 'package:flutter/material.dart';

import 'core/theme.dart';
import 'features/auth/auth_controller.dart';
import 'features/auth/auth_scope.dart';
import 'routing/app_router.dart';

class SduiHostApp extends StatefulWidget {
  const SduiHostApp({super.key, required this.auth});

  final AuthController auth;

  @override
  State<SduiHostApp> createState() => _SduiHostAppState();
}

class _SduiHostAppState extends State<SduiHostApp> {
  late final _router = createAppRouter(widget.auth);

  @override
  Widget build(BuildContext context) {
    return AuthScope(
      controller: widget.auth,
      child: MaterialApp.router(
        title: 'SDUI',
        theme: buildAppTheme(),
        routerConfig: _router,
        debugShowCheckedModeBanner: false,
      ),
    );
  }
}
