<table width="100%" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#F4F4F4"
       style="font-family:Helvetica;">
    <tbody>
    <tr>
        <td>
            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                   style="width: 100%; max-width: 600px;" align="center">
                <tbody>
                <tr>
                    <td style="padding: 0; color: #000000; text-align: left;" bgcolor="#ffffff" width="100%"
                        align="left">
                        <!-- Spacing -->
                        <table border="0" cellpadding="0" cellspacing="0" width="100%"
                               style="table-layout:fixed">
                            <tbody>
                            <tr>
                                <td style="padding:0px 0px 30px 0px" bgcolor="">
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <!-- Logo -->
                        <table border="0" cellpadding="0" cellspacing="0" width="100%"
                               style="table-layout:fixed">
                            <tbody>
                            <tr>
                                <td height="100%" valign="top">
                                    <div>
                                        <h1 style="text-align: center">VNBEAUTY</h1>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <!-- Header 1 -->
                        <table border="0" cellpadding="0" cellspacing="0" width="100%"
                               style="table-layout:fixed">
                            <tbody>
                            <tr>
                                <td height="100%" valign="top">
                                    <div style="padding: 32px 18px 24px;">
                                        <table style="width: 100%;">
                                            <tbody>
                                            <tr>
                                                <td>
                                                    <span style="font-size:24px">Đơn hàng của bạn đã hoàn thành!</span>
                                                </td>
                                                <td align="right">
                                                                        <span
                                                                            style="font-size:18px;color:#808080;font-weight:bold"></span>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <!-- Header 2 -->
                        <table border="0" cellpadding="0" cellspacing="0" width="100%"
                               style="table-layout:fixed">
                            <tbody>
                            <tr>
                                <td style="padding:0px 20px 18px 20px;line-height:22px;text-align:inherit"
                                    height="100%" valign="top" bgcolor="">
                                    <div>
                                        <div style="font-family:inherit;text-align:inherit">
                                                            <span
                                                                style="color:#222222;font-family:Helvetica;font-size:14px;line-height:22px">Cảm ơn bạn đã đặt hàng tại </span>
                                            <a href="#"
                                               target="_blank">
                                                                <span
                                                                    style="color:#4aabf7;font-family:Helvetica;font-size:14px;line-height:22px">VNBeauty</span></a><span
                                                style="color:#222222;font-family:Helvetica;font-size:14px;line-height:22px">. Bạn có thể liên hệ với người bán qua email: <a
                                                    href="mailto:{{ $user->email }}"
                                                    target="_blank">{{ $user->email }}</a>.</span><br>
                                            <br>
                                            <span
                                                style="color:#222222;font-family:Helvetica;font-size:14px;line-height:22px">Đây là những gì bạn đặt:</span>
                                        </div>
                                        <div></div>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <!-- HR -->
                        <table border="0" cellpadding="0" cellspacing="0" width="100%"
                               style="table-layout:fixed">
                            <tbody>
                            <tr>
                                <td style="padding:0px 0px 0px 0px" height="100%" valign="top"
                                    bgcolor="">
                                    <table border="0" cellpadding="0" cellspacing="0" align="center"
                                           width="100%" height="2px" style="line-height:2px;font-size:2px">
                                        <tbody>
                                        <tr>
                                            <td style="padding:0px 0px 2px 0px" bgcolor="#f0f0f0">
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <!-- Content 1 -->
                        <table border="0" cellpadding="0" cellspacing="0" width="100%"
                               style="table-layout:fixed">
                            <tbody>
                            <tr>
                                <td height="100%" valign="top">
                                    <div style="padding:18px 18px 24px">
                                        <table style="width:100%">
                                            <tbody>
                                            <tr>
                                                <td style="padding-bottom:16px"><span
                                                        style="font-weight:bold;white-space:pre-wrap">Phí vận chuyển</span>
                                                </td>
                                                <td style="padding-bottom:16px" align="right">{{ number_format(30000) }}đ
                                                </td>
                                            </tr>

                                            @foreach($orderItems as $item)
                                                <tr>
                                                    <td style="padding-bottom:16px">
                                                    <span style="font-weight:bold;">
                                                        {{ $item->quantity }} x {{ $item->product_name}}{{$item->option_name != null? ", " . $item->option_name : ""}}
                                                    </span>
                                                    </td>
                                                    <td style="padding-bottom:16px"
                                                        align="right">{{ number_format($item->quantity * $item->option_price) }}đ</td>
                                                </tr>
                                            @endforeach

                                            <tr>
                                                <td><span style="font-weight:bold">Tổng</span></td>
                                                <td align="right">{{ number_format($totalPrice + 30000) }}đ</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <!-- HR -->
                        <table border="0" cellpadding="0" cellspacing="0" width="100%"
                               style="table-layout:fixed">
                            <tbody>
                            <tr>
                                <td style="padding:0px 0px 0px 0px" height="100%" valign="top"
                                    bgcolor="">
                                    <table border="0" cellpadding="0" cellspacing="0" align="center"
                                           width="100%" height="2px" style="line-height:2px;font-size:2px">
                                        <tbody>
                                        <tr>
                                            <td style="padding:0px 0px 2px 0px" bgcolor="#f0f0f0">
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <!-- Content 2 -->
                        <table border="0" cellpadding="0" cellspacing="0" width="100%"
                               style="table-layout:fixed">
                            <tbody>
                            <tr>
                                <td height="100%" valign="top">
                                    <div style="padding:18px 18px 10px">
                                        <table style="width:100%">
                                            <tbody>
                                            <tr>
                                                <td style=""><span
                                                        style="font-weight:bold">Tên của bạn</span>
                                                </td>
                                                <td align="right"
                                                    style="white-space:pre-wrap">
                                                    {{ $order->full_name }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style=""><span
                                                        style="font-weight:bold">Email</span>
                                                </td>
                                                <td align="right"
                                                    style="white-space:pre-wrap">
                                                    {{ $order->email }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style=""><span
                                                        style="font-weight:bold">Số điện thoại</span>
                                                </td>
                                                <td align="right"
                                                    style="white-space:pre-wrap">
                                                    {{ $order->phone_number }}
                                                </td>
                                            </tr>

                                            <tr>
                                                <td style=""><span
                                                        style="font-weight:bold">Địa chỉ</span></td>
                                                <td align="right"
                                                    style="white-space:pre-wrap">
                                                    {{ $order->address }}
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <!-- Spacing -->
                        <table border="0" cellpadding="0" cellspacing="0" width="100%"
                               style="table-layout:fixed">
                            <tbody>
                            <tr>
                                <td style="padding:0px 20px 18px 20px;line-height:22px;text-align:inherit"
                                    height="100%" valign="top" bgcolor="">
                                    <div>
                                        <div style="font-family:inherit;text-align:inherit"></div>
                                        <div></div>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <!-- HR -->
                        <table border="0" cellpadding="0" cellspacing="0" width="100%"
                               style="table-layout:fixed">
                            <tbody>
                            <tr>
                                <td style="padding:0px 0px 0px 0px" height="100%" valign="top"
                                    bgcolor="">
                                    <table border="0" cellpadding="0" cellspacing="0" align="center"
                                           width="100%" height="2px" style="line-height:2px;font-size:2px">
                                        <tbody>
                                        <tr>
                                            <td style="padding:0px 0px 2px 0px" bgcolor="#f0f0f0">
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <!-- Footer 1 -->
                        <table border="0" cellpadding="0" cellspacing="0" width="100%"
                               style="table-layout:fixed">
                            <tbody>
                            <tr>
                                <td style="padding:18px 20px 18px 20px;line-height:22px;text-align:inherit"
                                    height="100%" valign="top" bgcolor="">
                                    <div>
                                        <div style="font-family:inherit;text-align:inherit"><span
                                                style="color:#222222;font-family:Helvetica;font-size:14px;line-height:24px">Nếu bạn có câu hỏi, thắc mắc nào, vui lòng liên hệ </span><span
                                                style="color:#222222;font-family:Helvetica;font-size:14px;line-height:24px"><strong><a
                                                        href="mailto:{{ $user->email }}"
                                                        target="_blank">{{ $user->email }}</a></strong></span><span
                                                style="color:#222222;font-family:Helvetica;font-size:14px;line-height:24px">.</span><br>
                                            <br>
                                            <span
                                                style="color:#222222;font-family:Helvetica;font-size:14px;line-height:24px">Chúc bạn 1 ngày tốt lành!</span>
                                        </div>
                                        <div></div>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <!-- Spacing -->
                        <table border="0" cellpadding="0" cellspacing="0" width="100%"
                               style="table-layout:fixed">
                            <tbody>
                            <tr>
                                <td height="100%" valign="top">
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <!-- Footer 2 -->
                        <table border="0" cellpadding="0" cellspacing="0" width="100%"
                               style="table-layout:fixed">
                            <tbody>
                            <tr>
                                <td height="100%" valign="top">
                                    <div
                                        style="font-family:inherit;text-align:center;padding:16px 0px 16px">
                                        <a href="#"
                                           target="_blank"><span
                                                style="color:#222222;font-family:Helvetica;font-size:14px;font-style:normal;font-variant-ligatures:normal;font-variant-caps:normal;font-weight:400;letter-spacing:normal;text-align:center;text-indent:0px;text-transform:none;white-space:normal;word-spacing:0px;background-color:rgb(255,255,255);text-decoration-line:none;text-decoration-style:initial;text-decoration-color:initial;line-height:22px"><strong>Cung cấp bởi VNBEAUTY</strong></span></a>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>
