import {
  AppWindow,
  ChevronLeft,
  ChevronRight,
  ExternalLink,
  Files,
  FileText,
  Folder,
  House,
  Inbox,
  Info,
  LayoutGrid,
  LayoutPanelLeft,
  Link as LinkIcon,
  MessageSquare,
  Puzzle,
  Settings,
  SquarePen,
  Tags,
  Users,
  type LucideIcon,
} from "lucide-react"

const map: Record<string, LucideIcon> = {
  house: House,
  "square-pen": SquarePen,
  files: Files,
  "file-text": FileText,
  folder: Folder,
  tags: Tags,
  "message-square": MessageSquare,
  inbox: Inbox,
  users: Users,
  "layout-grid": LayoutGrid,
  "layout-panel-left": LayoutPanelLeft,
  puzzle: Puzzle,
  settings: Settings,
  "app-window": AppWindow,
  info: Info,
  link: LinkIcon,
  "chevron-left": ChevronLeft,
  "chevron-right": ChevronRight,
  external: ExternalLink,
}

interface NavIconProps {
  name?: string
  className?: string
}

export function NavIcon({ name, className }: NavIconProps) {
  const Icon = (name && map[name]) || LinkIcon

  return <Icon className={className} strokeWidth={1.75} aria-hidden="true" />
}
