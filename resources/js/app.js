import './bootstrap';

import {
    Livewire,
    Alpine,
} from '../../vendor/livewire/livewire/dist/livewire.esm';
import Swal from 'sweetalert2';

window.Alpine = Alpine;
window.Swal = Swal;

window.showToast = (
    icon = 'success',
    title = ''
) => {
    if (!title) {
        return;
    }

    Swal.fire({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        icon,
        title,
    });

};

window.confirmAction = async ({
    title = 'Konfirmasi tindakan',
    text = 'Tindakan ini akan diproses.',
    confirmText = 'Ya, lanjutkan',
    cancelText = 'Batal',
    icon = 'question',
    tone = 'primary',
} = {}) => {
    const isDark =
        document.documentElement.classList.contains('dark');

    const toneMap = {
        primary: {
            confirm: '#2563eb',
            iconBg: isDark ? 'rgba(37, 99, 235, .14)' : '#dbeafe',
            iconColor: '#2563eb',
        },
        success: {
            confirm: '#059669',
            iconBg: isDark ? 'rgba(5, 150, 105, .14)' : '#d1fae5',
            iconColor: '#059669',
        },
        warning: {
            confirm: '#d97706',
            iconBg: isDark ? 'rgba(217, 119, 6, .16)' : '#fef3c7',
            iconColor: '#d97706',
        },
        danger: {
            confirm: '#e11d48',
            iconBg: isDark ? 'rgba(225, 29, 72, .14)' : '#ffe4e6',
            iconColor: '#e11d48',
        },
    };

    const selectedTone =
        toneMap[tone] || toneMap.primary;

    const result = await Swal.fire({
        title,
        text,
        icon,
        showCancelButton: true,
        confirmButtonText: confirmText,
        cancelButtonText: cancelText,
        reverseButtons: true,
        focusCancel: true,
        buttonsStyling: false,
        background: isDark ? '#0f172a' : '#ffffff',
        color: isDark ? '#f8fafc' : '#0f172a',
        customClass: {
            popup: 'hadirku-confirm-popup',
            title: 'hadirku-confirm-title',
            htmlContainer: 'hadirku-confirm-text',
            actions: 'hadirku-confirm-actions',
            confirmButton: 'hadirku-confirm-button',
            cancelButton: 'hadirku-cancel-button',
        },
        didOpen: (popup) => {
            popup.style.setProperty(
                '--hadirku-confirm-color',
                selectedTone.confirm
            );
            popup.style.setProperty(
                '--hadirku-confirm-icon-bg',
                selectedTone.iconBg
            );
            popup.style.setProperty(
                '--hadirku-confirm-icon-color',
                selectedTone.iconColor
            );
        },
    });

    return result.isConfirmed;
};

Livewire.start();
