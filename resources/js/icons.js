/**
 * Centralized Lucide Icons – single place to register and replace icons across the app.
 * Use data-lucide="icon-name" (kebab-case) in HTML; icons are replaced on load.
 * Call window.replaceLucideIcons() after injecting dynamic HTML to replace new icons.
 */
import {
  createIcons,
  Mail,
  Lock,
  Eye,
  EyeOff,
  Clock,
  Printer,
  X,
  Info,
  Sun,
  Moon,
  LayoutDashboard,
  ClipboardList,
  MapPin,
  FileText,
  BarChart3,
  FileBarChart,
  Settings,
  LogOut,
  Menu,
  Users,
  Plus,
  Search,
  TrendingUp,
  TrendingDown,
  Loader2,
  Leaf,
  CheckCircle,
  CircleX,
  ChevronDown,
  Download,
  FileSpreadsheet,
  Pencil,
  Trash2,
  Building2,
  RefreshCw,
  Panda,
} from 'lucide';

const ICONS = {
  Mail,
  Lock,
  Eye,
  EyeOff,
  Clock,
  Printer,
  X,
  Info,
  Sun,
  Moon,
  LayoutDashboard,
  ClipboardList,
  MapPin,
  FileText,
  BarChart3,
  FileBarChart,
  Settings,
  LogOut,
  Menu,
  Users,
  Plus,
  Search,
  TrendingUp,
  TrendingDown,
  Loader2,
  Leaf,
  CheckCircle,
  CircleX,
  ChevronDown,
  Download,
  FileSpreadsheet,
  Pencil,
  Trash2,
  Building2,
  RefreshCw,
  Panda,
};

const defaultAttrs = {
  'stroke-width': 1.75,
  'aria-hidden': 'true',
};

function replaceIcons(root = document) {
  createIcons({
    icons: ICONS,
    attrs: defaultAttrs,
    nameAttr: 'data-lucide',
    root,
  });
}

replaceIcons();

if (typeof document !== 'undefined') {
  document.addEventListener('DOMContentLoaded', () => replaceIcons());
}

if (typeof window !== 'undefined') {
  window.replaceLucideIcons = (element) => {
    replaceIcons(element || document);
  };
}

export { replaceIcons, ICONS };
