# sdui_app

Host app for the SDUI Stac client.

## Fixtures (default)

```bash
flutter run
```

Login and register are local mocks. Screens load from `assets/screens/`.

## API (Phase 2)

Start [Api-SDUI-App](../../../Backend/Owner/Api-SDUI-App), then:

```bash
flutter run --dart-define=SDUI_USE_NETWORK=true --dart-define=SDUI_API_BASE_URL=http://127.0.0.1:8000
```

o

```bash
flutter run --dart-define=SDUI_USE_NETWORK=true --dart-define=SDUI_API_BASE_URL=http://10.0.2.2:8000
```

Auth goes to Sanctum; screens come from `GET /sdui/screens/{name}` as a versioned envelope (`schemaVersion` + Stac `body`). Bundled JSON in `assets/screens/` matches those same PHP screens and stays as a fallback when the flag is off.

`sduiLogout` calls `POST /auth/logout` and then clears the local token. HTTP 401 only drops the local session (`clearSession`) so a failed revoke cannot loop.
