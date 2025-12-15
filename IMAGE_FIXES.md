# Исправления обработки изображений и редактирования

## Исправленные проблемы

### 1. Валидация изображений

**Проблема:** Validation failed при загрузке изображений через FormData.

**Решение:** 
- Убрана валидация `images.*` из основного `validate()`
- Добавлена отдельная валидация каждого файла после проверки наличия
- Валидация происходит только для валидных файлов

```php
// Валидация изображений отдельно, чтобы не было проблем с FormData
if ($r->hasFile('images')) {
    $files = $r->file('images');
    $filesArray = is_array($files) ? $files : [$files];
    
    foreach ($filesArray as $file) {
        if ($file && $file->isValid()) {
            $validator = validator(['image' => $file], [
                'image' => 'required|image|max:5120'
            ]);
            
            if ($validator->fails()) {
                return ApiResponse::error(...);
            }
        }
    }
}
```

### 2. Обработка ошибок Go Image Service

**Проблема:** Ошибки при обработке изображений не логировались и не сообщались пользователю.

**Решение:**
- Добавлен try-catch для обработки исключений
- Добавлено логирование ошибок
- Если часть изображений не обработалась, объявление все равно создается с предупреждением
- Добавлена проверка на пустой ответ от сервиса
- Добавлен timeout для HTTP запросов (30 секунд)

```php
$imageErrors = [];

foreach ($imageFiles as $file) {
    try {
        $response = Http::timeout(30)->attach(...)->post($imageServiceUrl);
        
        if ($response->failed()) {
            \Log::warning('Image service failed', [...]);
            $imageErrors[] = $file->getClientOriginalName() . ': Image processing failed';
            continue;
        }
        
        // ... обработка успешного ответа
    } catch (\Exception $e) {
        \Log::error('Image processing error', [...]);
        $imageErrors[] = $file->getClientOriginalName() . ': ' . $e->getMessage();
        continue;
    }
}

// Если были ошибки, но объявление создано - возвращаем успех с предупреждением
if (!empty($imageErrors) && count($imageFiles) > 0) {
    return ApiResponse::success(..., 'Ad created, but some images failed: ' . implode(', ', $imageErrors), 201);
}
```

### 3. Ошибка редактирования объявлений

**Проблема:** При нажатии "Редактировать" показывалась ошибка "Ошибка загрузки объявления".

**Решение:**
- Улучшена обработка ошибок в `EditAd.jsx`
- Добавлена проверка статусов ошибок (404, 401)
- Более понятные сообщения об ошибках
- Исправлен метод `show()` в `AdController` - теперь возвращает правильные ошибки вместо `abort(404)`

```jsx
// EditAd.jsx
catch (error) {
  if (error.response?.status === 404) {
    setError('Объявление не найдено или у вас нет доступа к его редактированию')
  } else if (error.response?.status === 401) {
    setError('Необходима авторизация')
  } else {
    setError(error.response?.data?.message || 'Ошибка загрузки объявления')
  }
}
```

```php
// AdController.php - show()
if ($ad->status !== 'approved') {
    $user = auth()->user();
    
    if (!$user || ($user->id !== $ad->user_id && $user->role !== 'admin')) {
        return ApiResponse::error('Объявление не найдено или у вас нет доступа', null, 404);
    }
}
```

### 4. URL Go Image Service

**Проблема:** Неправильный URL для Go image service.

**Решение:**
- Исправлен URL в `.env`: `http://localhost:8080/process` (для локального сервера)
- Добавлен проброс порта в `docker-compose.yml`: `8080:8080`
- Обновлен дефолтный URL в `config/services.php`

**Важно:**
- Для локального Laravel сервера: `http://localhost:8080/process`
- Для Docker контейнера: `http://go-image-service:8080/process`

### 5. Улучшена обработка ошибок валидации во фронтенде

**Решение:**
- В `CreateAd.jsx` добавлен сбор всех ошибок валидации в один текст
- Показываются все ошибки валидации, а не только первая

```jsx
if (errors) {
  const errorTexts = Object.entries(errors).flatMap(([field, messages]) => 
    Array.isArray(messages) ? messages : [messages]
  )
  setError(errorTexts.join(', ') || errorMessage)
}
```

## Настройки

### .env для локального сервера
```env
IMAGE_SERVICE_URL=http://localhost:8080/process
```

### .env для Docker контейнера
```env
IMAGE_SERVICE_URL=http://go-image-service:8080/process
```

## Проверка работы

1. Создание объявления с изображениями должно работать без ошибок
2. Редактирование объявлений на модерации должно работать для владельца
3. Ошибки обработки изображений логируются и сообщаются пользователю
4. Объявление создается даже если часть изображений не обработалась

