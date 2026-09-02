import 'package:dio/dio.dart';
import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:sdui_client/sdui_client.dart';

import 'app.dart';
import 'features/auth/auth_controller.dart';
import 'features/auth/mock_auth_repository.dart';
import 'features/auth/sanctum_auth_repository.dart';
import 'features/auth/secure_token_store.dart';

Future<void> main() async {
  WidgetsFlutterBinding.ensureInitialized();

  const useNetwork = bool.fromEnvironment('SDUI_USE_NETWORK');
  const apiBaseUrl = String.fromEnvironment(
    'SDUI_API_BASE_URL',
    defaultValue: 'http://127.0.0.1:8000',
  );

  final dio = Dio(
    BaseOptions(
      baseUrl: apiBaseUrl,
      headers: const {'Accept': 'application/json'},
    ),
  );
  final tokenStore = SecureTokenStore();
  final auth = AuthController(
    repository:
        useNetwork ? SanctumAuthRepository(dio) : MockAuthRepository(),
    tokenStore: tokenStore,
  );

  await Sdui.initialize(
    config: SduiConfig(
      source: useNetwork
          ? SduiScreenSource.network
          : SduiScreenSource.asset,
      baseUrl: apiBaseUrl,
      tokenStore: tokenStore,
      dio: dio,
      onUnauthorized: auth.clearSession,
      onLogout: auth.logout,
      onNavigateScreen: (context, screen, {style = 'push'}) {
        final location = SduiRoutes.screen(screen);
        if (style == 'replace') {
          context.go(location);
        } else {
          context.push(location);
        }
      },
    ),
  );

  await auth.restore();
  runApp(SduiHostApp(auth: auth));
}
