import type { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from '@lucide/vue';

export type BreadcrumbItem = {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
};

export type NavItem = {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon;
    isActive?: boolean;
    /** Warna aksen ikon (nilai CSS, mis. var(--color-chart-2)). */
    color?: string;
    /** Tampilkan sebagai menu utama yang menonjol. */
    highlight?: boolean;
};
