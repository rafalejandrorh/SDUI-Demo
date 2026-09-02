import 'package:flutter/foundation.dart';
import 'package:sdui_client/sdui_client.dart';

import 'auth_repository.dart';

class AuthController extends ChangeNotifier {
  AuthController({
    required AuthRepository repository,
    required TokenStore tokenStore,
  })  : _repository = repository,
        _tokenStore = tokenStore;

  final AuthRepository _repository;
  final TokenStore _tokenStore;
  String? _token;

  bool get isLoggedIn => _token != null && _token!.isNotEmpty;

  Future<void> restore() async {
    _token = await _tokenStore.read();
    notifyListeners();
  }

  /// Drop the local session without calling the API.
  /// Use this for HTTP 401 ([SduiConfig.onUnauthorized]).
  Future<void> clearSession() async {
    await _tokenStore.clear();
    _token = null;
    notifyListeners();
  }

  /// Revoke the server token (`POST /auth/logout`) then [clearSession].
  /// Use this for the Stac `sduiLogout` action ([SduiConfig.onLogout]).
  Future<void> logout() async {
    try {
      await _repository.logout();
    } catch (_) {
      // Always drop the local session even if revoke fails.
    }
    await clearSession();
  }

  Future<void> login({
    required String email,
    required String password,
  }) async {
    final token = await _repository.login(email: email, password: password);
    await _tokenStore.write(token);
    _token = token;
    notifyListeners();
  }

  Future<void> register({
    required String name,
    required String email,
    required String password,
    String? passwordConfirmation,
  }) async {
    final token = await _repository.register(
      name: name,
      email: email,
      password: password,
      passwordConfirmation: passwordConfirmation,
    );
    await _tokenStore.write(token);
    _token = token;
    notifyListeners();
  }
}
