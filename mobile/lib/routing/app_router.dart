import 'package:go_router/go_router.dart';
import 'package:sdui_client/sdui_client.dart';

import '../features/auth/auth_controller.dart';
import '../features/auth/login_screen.dart';
import '../features/auth/register_screen.dart';
import 'app_routes.dart';

GoRouter createAppRouter(AuthController auth) {
  return GoRouter(
    initialLocation: AppRoutes.login,
    refreshListenable: auth,
    redirect: (context, state) {
      final loggingIn = state.matchedLocation == AppRoutes.login ||
          state.matchedLocation == AppRoutes.register;
      if (!auth.isLoggedIn && !loggingIn) {
        return AppRoutes.login;
      }
      if (auth.isLoggedIn && loggingIn) {
        return SduiRoutes.screen('home');
      }
      return null;
    },
    routes: [
      GoRoute(
        path: AppRoutes.login,
        builder: (context, state) => const LoginScreen(),
      ),
      GoRoute(
        path: AppRoutes.register,
        builder: (context, state) => const RegisterScreen(),
      ),
      GoRoute(
        path: '${SduiRoutes.screenPrefix}/:screen',
        builder: (context, state) {
          final name = state.pathParameters['screen'] ?? 'home';
          return DynamicScreen(name: name);
        },
      ),
    ],
  );
}
