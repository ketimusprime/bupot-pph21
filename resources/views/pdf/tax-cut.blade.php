<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bukti Potong PPh Pasal 21 - {{ $taxCut->memo_number }}</title>
    <style>
        @page { margin: 20mm 15mm; }
        body { font-family: Arial, sans-serif; font-size: 11pt; line-height: 1.3; color: #000; }

        .header { text-align: center; margin-bottom: 5mm; border: 2px solid #000; padding: 3mm; }
        .header h2 { margin: 0 0 2mm 0; font-size: 12pt; font-weight: bold; }

        .memo-to { margin-bottom: 5mm; }
        .memo-to strong { font-size: 12pt; }

        .content { border: 2px solid #000; padding: 5mm; }

        .title { text-align: center; margin-bottom: 5mm; }
        .title h3 { margin: 0; font-size: 13pt; font-weight: bold; }
        .title p { margin: 2mm 0 0 0; font-size: 11pt; }

        .details { margin-bottom: 5mm; }
        .detail-row { display: table; width: 100%; margin-bottom: 3mm; }
        .detail-label { display: table-cell; width: 40%; vertical-align: top; }
        .detail-value { display: table-cell; width: 60%; vertical-align: top; }

        .amounts { margin-top: 8mm; }
        .amount-row { display: table; width: 100%; margin-bottom: 3mm; }
        .amount-label { display: table-cell; width: 60%; vertical-align: top; }
        .amount-value { display: table-cell; width: 40%; text-align: right; vertical-align: top; font-weight: bold; }

        .approval { margin-top: 10mm; text-align: right; }
        .signature { margin-top: 15mm; text-align: right; font-weight: bold; }

        .footer { margin-top: 5mm; font-size: 9pt; text-align: center; }
        .number-format { font-family: 'Courier New', monospace; }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="header">
        <h2>{{ $taxCut->company->name }}</h2>
        <p style="margin: 0; font-size: 10pt;">{{ $taxCut->company->address }}</p>
    </div>

    {{-- Memo To --}}
    <div class="memo-to">
        <strong>MEMO INTERN</strong><br>
        To: {{ $taxCut->recipient->name }}
    </div>

    {{-- Content --}}
    <div class="content">

        {{-- Title --}}
        <div class="title">
            <h3>Perhitungan Potongan PPh Pasal 21</h3>
            <p>
                Komisi Freelance (
                {{ $taxCut->pph_method === 'gross_up'
                    ? 'Gross Up (PPh Ditanggung Perusahaan)'
                    : 'Gross (PPh Dipotong dari Komisi)' }}
                )
            </p>
        </div>

        {{-- Details --}}
        <div class="details">
            <div class="detail-row">
                <div class="detail-label">Nama</div>
                <div class="detail-value">: {{ $taxCut->recipient->name }}</div>
            </div>

            <div class="detail-row">
                <div class="detail-label">NPWP</div>
                <div class="detail-value">
                    :
                    @if(!empty($taxCut->recipient->npwp))
                        {{ $taxCut->recipient->formatted_npwp ?? $taxCut->recipient->npwp }}
                    @else
                        Tidak punya NPWP
                    @endif
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-label">No. Memo</div>
                <div class="detail-value">: {{ $taxCut->memo_number }}</div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Tanggal Dipotong</div>
                <div class="detail-value">: {{ $taxCut->cut_date->format('d/m/Y') }}</div>
            </div>

            <div class="detail-row">
                <div class="detail-label">No. Invoice</div>
                <div class="detail-value">: {{ $taxCut->invoice_number ?? '-' }}</div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Tgl Invoice</div>
                <div class="detail-value">: {{ $taxCut->invoice_date ? $taxCut->invoice_date->format('d/m/Y') : '-' }}</div>
            </div>
        </div>

        {{-- Amounts --}}
        <div class="amounts">
            <div class="amount-row">
                <div class="amount-label">Komisi Freelance:</div>
                <div class="amount-value number-format">{{ number_format($taxCut->commission_amount, 0, ',', ',') }}</div>
            </div>

            <div class="amount-row">
                <div class="amount-label">DPP Pemotongan ({{ $taxCut->tax_rate }}%) :</div>
                <div class="amount-value number-format">{{ number_format($taxCut->dpp_amount, 0, ',', ',') }}</div>
            </div>

            <div class="amount-row">
                <div class="amount-label">Pot.PPh21:</div>
                <div class="amount-value number-format">{{ number_format($taxCut->tax_amount, 0, ',', '.') }}</div>
            </div>

            <div class="amount-row" style="font-weight: bold; font-size: 12pt;">
                <div class="amount-label">Total Pembayaran:</div>
                <div class="amount-value number-format">{{ number_format($taxCut->net_payment, 0, ',', '.') }}</div>
            </div>
        </div>

        {{-- Approval --}}
        @if($taxCut->approved_by)
        <div class="approval">
            Approved by:<br>
            <div class="signature">{{ $taxCut->approved_by }}</div>
        </div>
        @endif

    </div>

    {{-- Footer --}}
    <div class="footer">
        @if($taxCut->deposited_date)
            Catatan: Dipotong {{ $taxCut->cut_date->format('d/m/Y') }} & setor ke K.Negara {{ $taxCut->deposited_date->format('d/m/Y') }}
        @else
            Catatan: Dipotong {{ $taxCut->cut_date->format('d/m/Y') }}
        @endif
    </div>

</body>
</html>
