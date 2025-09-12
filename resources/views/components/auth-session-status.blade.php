@props(['status'])
<!--- Oturum durumu mesajlarını görüntülemek için kullanılır. Örneğin, kullanıcı başarılı bir şekilde giriş yaptığında veya çıkış yaptığında gösterilir. --->
@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-green-600']) }}>
        {{ $status }}
    </div>
@endif
