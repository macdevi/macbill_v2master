import contextlib
import hashlib
import io
import json
import os
from pathlib import Path
import sqlite3
import sys
import uuid
from datetime import datetime, timezone

PLANNER_CODE = 'import json\nimport sys\nimport hashlib\nfrom pathlib import Path\nimport sqlite3\n\ndef quote(name):\n    return \'"\' + name.replace(\'"\', \'""\') + \'"\'\nbase = sqlite3.connect(\':memory:\')\nwith sqlite3.connect(Path(sys.argv[1]).resolve().as_uri() + \'?mode=ro\', uri=True) as source:\n    source.backup(base)\nbase.row_factory = sqlite3.Row\ntables = [row[0] for row in base.execute("\\n        SELECT name FROM sqlite_master\\n        WHERE type=\'table\' AND name NOT LIKE \'sqlite_%\'\\n        ORDER BY name\\n    ")]\ncolumns = {table: [row[1] for row in base.execute(f\'PRAGMA table_info({quote(table)})\')] for table in tables}\n\ndef rows(db, table):\n    return [dict(row) for row in db.execute(f\'SELECT * FROM {quote(table)}\')]\n\ndef ids_matching(data, column, values):\n    return {row[\'id\'] for row in data if row.get(column) is not None and row.get(column) in values}\n\ndef normalized(data):\n    return sorted((tuple(row.values()) for row in data), key=repr)\nbefore = {table: rows(base, table) for table in tables}\nif list(base.execute(\'PRAGMA foreign_key_check\')):\n    raise SystemExit(\'STOP: database awal memiliki pelanggaran foreign key.\')\nplans = {}\nfor router in before[\'routers\']:\n    if router[\'id\'] != int(sys.argv[2]):\n        continue\n    rid = router[\'id\']\n    pass\n    customer_ids = ids_matching(before[\'customers\'], \'router_id\', {rid})\n    customers_by_id = {row[\'id\']: row for row in before[\'customers\']}\n    cancellations = before[\'installation_cancellations\']\n    cancel_ids = ids_matching(cancellations, \'original_router_id\', {rid}) | ids_matching(cancellations, \'original_customer_id\', customer_ids)\n    selected_cancellations = [row for row in cancellations if row[\'id\'] in cancel_ids]\n    conflicts = [row for row in selected_cancellations if row.get(\'original_customer_id\') in customers_by_id and customers_by_id[row[\'original_customer_id\']][\'router_id\'] != rid]\n    if conflicts:\n        pass\n        continue\n    historical_customer_ids = customer_ids | {row[\'original_customer_id\'] for row in selected_cancellations if row.get(\'original_customer_id\') is not None}\n    installation_ids = ids_matching(before[\'installations\'], \'customer_id\', customer_ids)\n    historical_installation_ids = installation_ids | {row[\'original_installation_id\'] for row in selected_cancellations if row.get(\'original_installation_id\') is not None}\n    existing_installations = {row[\'id\']: row for row in before[\'installations\']}\n    ambiguous = [existing_installations[i] for i in historical_installation_ids if i in existing_installations and existing_installations[i].get(\'customer_id\') is not None and (existing_installations[i][\'customer_id\'] not in customer_ids)]\n    if ambiguous:\n        pass\n        continue\n    installation_ids |= {i for i in historical_installation_ids if i in existing_installations}\n    payment_ids = ids_matching(before[\'installation_payments\'], \'installation_id\', installation_ids) | ids_matching(before[\'installation_payments\'], \'original_installation_id\', historical_installation_ids) | ids_matching(before[\'installation_payments\'], \'original_customer_id\', historical_customer_ids)\n    invoice_ids = ids_matching(before[\'invoices\'], \'customer_id\', customer_ids)\n    links = before[\'internet_package_router\']\n    linked_packages = {row[\'internet_package_id\'] for row in links if row[\'router_id\'] == rid}\n    preserved_packages = {row[\'internet_package_id\'] for row in links if row[\'router_id\'] != rid} | {row[\'internet_package_id\'] for row in before[\'customers\'] if row[\'id\'] not in customer_ids} | {row[\'internet_package_id\'] for row in before[\'package_sync_states\'] if row[\'router_id\'] != rid}\n    package_ids = linked_packages - preserved_packages\n    targets = {\'installation_payments\': payment_ids, \'installation_cancellations\': cancel_ids, \'installations\': installation_ids, \'payments\': ids_matching(before[\'payments\'], \'invoice_id\', invoice_ids), \'credit_balance_transactions\': ids_matching(before[\'credit_balance_transactions\'], \'customer_id\', customer_ids), \'invoices\': invoice_ids, \'customers\': customer_ids, \'package_sync_states\': {row[\'id\'] for row in before[\'package_sync_states\'] if row[\'router_id\'] == rid or row[\'internet_package_id\'] in package_ids}, \'internet_package_router\': {row[\'id\'] for row in links if row[\'router_id\'] == rid or row[\'internet_package_id\'] in package_ids}, \'internet_packages\': package_ids, \'routers\': {rid}}\n    if \'area_router_access\' in before:\n        access_ids = {row[\'id\'] for row in before[\'area_router_access\'] if row[\'router_id\'] == rid}\n        targets = {**{table: selected for table, selected in targets.items() if table != \'routers\'}, \'area_router_access\': access_ids, \'routers\': {rid}}\n    plans[str(rid)] = {\'counts\': {table: len(selected_ids) for table, selected_ids in targets.items()}, \'preserved_packages\': len(linked_packages - package_ids)}\nbase.close()\nrequested = str(int(sys.argv[2]))\nif requested not in plans:\n    raise SystemExit(\'Rencana tidak dapat dibuat karena relasi data tidak konsisten.\')\nprint(json.dumps(plans[requested], separators=(\',\', \':\')))'
os.umask(0o077)

def canonical(value):
    return json.dumps(value, sort_keys=True, separators=(",", ":"), default=str)

def normalized(rows):
    return sorted(canonical(row) for row in rows)

def plan(database, router_id):
    previous = sys.argv
    namespace = {}
    output = io.StringIO()
    try:
        sys.argv = ["planner", str(database), str(router_id)]
        with contextlib.redirect_stdout(output):
            exec(compile(PLANNER_CODE, "<tested-planner>", "exec"), namespace)
    finally:
        sys.argv = previous
    summary = json.loads(output.getvalue())
    before, targets = namespace["before"], namespace["targets"]
    owned_customers = targets["customers"]
    owned_installations = targets["installations"]
    customers = {r["id"]: r for r in before["customers"]}
    installations = {r["id"]: r for r in before["installations"]}
    for row in before["installation_payments"]:
        if row["id"] not in targets["installation_payments"]:
            continue
        for field in ("installation_id", "original_installation_id"):
            value = row.get(field)
            if value in installations and value not in owned_installations:
                raise RuntimeError("Pembayaran pasang baru merujuk instalasi di luar sasaran.")
        value = row.get("original_customer_id")
        if value in customers and value not in owned_customers:
            raise RuntimeError("Pembayaran pasang baru merujuk pelanggan di luar sasaran.")
    relevant = {
        table: normalized(before[table]) for table in sorted(targets)
    }
    summary["fingerprint"] = hashlib.sha256(canonical({
        "router": router_id, "data": relevant,
        "schema": namespace["columns"],
    }).encode()).hexdigest()
    summary["ok"] = True
    return summary, before, targets

def readonly(database):
    return sqlite3.connect(database.as_uri() + "?mode=ro", uri=True, timeout=15)

def snapshot(source, destination):
    with readonly(source) as src, sqlite3.connect(destination) as dst:
        src.backup(dst)
        if dst.execute("PRAGMA integrity_check").fetchone()[0] != "ok":
            raise RuntimeError("Integritas backup gagal.")
        if dst.execute("PRAGMA foreign_key_check").fetchall():
            raise RuntimeError("Foreign key backup tidak valid.")

def save_receipt(path, data):
    temporary = path.with_suffix(".tmp")
    with temporary.open("w") as handle:
        json.dump(data, handle, indent=2)
        handle.flush()
        os.fsync(handle.fileno())
    os.replace(temporary, path)

def execute(database, router_id, payload):
    # mode=rw melarang SQLite membuat database baru bila path salah.
    db = sqlite3.connect(database.as_uri() + "?mode=rw", uri=True,
                         timeout=15, isolation_level=None)
    db.row_factory = sqlite3.Row
    committed = False
    result = None
    try:
        db.execute("PRAGMA foreign_keys=ON")
        if db.execute("PRAGMA foreign_keys").fetchone()[0] != 1:
            raise RuntimeError("Foreign key tidak aktif.")
        db.execute("BEGIN IMMEDIATE")
        summary, before, targets = plan(database, router_id)
        if summary["fingerprint"] != payload.get("fingerprint"):
            db.rollback()
            return {"ok": False, "stale": True}
        router = next(r for r in before["routers"] if r["id"] == router_id)
        if router["name"] != payload.get("confirm_name"):
            db.rollback()
            return {"ok": False, "name_mismatch": True}

        root = Path(payload["backup_dir"])
        root.mkdir(mode=0o700, parents=True, exist_ok=True)
        if root.is_symlink():
            raise RuntimeError("Direktori backup tidak boleh berupa symlink.")
        root.chmod(0o700)
        backup_id = datetime.now(timezone.utc).strftime("%Y%m%dT%H%M%SZ") + "-" + uuid.uuid4().hex
        folder = root / backup_id
        folder.mkdir(mode=0o700)
        backup = folder / "database.sqlite"
        snapshot(database, backup)
        backup.chmod(0o600)
        with backup.open("rb") as handle:
            digest = hashlib.sha256()
            for block in iter(lambda: handle.read(1024 * 1024), b""):
                digest.update(block)
            os.fsync(handle.fileno())
        receipt_path = folder / "receipt.json"
        receipt = {
            "status": "backup_complete_execution_pending",
            "backup_id": backup_id, "router_id": router_id,
            "actor_id": payload.get("actor_id"),
            "sha256": digest.hexdigest(), "plan": summary,
        }
        save_receipt(receipt_path, receipt)
        directory_fd = os.open(str(folder), os.O_RDONLY)
        try:
            os.fsync(directory_fd)
        finally:
            os.close(directory_fd)

        for table, selected in targets.items():
            quoted = '"' + table.replace('"', '""') + '"'
            db.executemany(
                f"DELETE FROM {quoted} WHERE id=?",
                [(identifier,) for identifier in sorted(selected)],
            )
        if db.execute("PRAGMA foreign_key_check").fetchall():
            raise RuntimeError("Foreign key tidak valid setelah penghapusan.")
        for table, original in before.items():
            quoted = '"' + table.replace('"', '""') + '"'
            actual = [dict(row) for row in db.execute(f"SELECT * FROM {quoted}")]
            expected = [r for r in original if r.get("id") not in targets.get(table, set())]
            if normalized(actual) != normalized(expected):
                raise RuntimeError("Perubahan di luar rencana pada tabel " + table)
        db.commit()
        committed = True
        result = {"ok": True, "committed": True, "backup_id": backup_id,
                  "counts": summary["counts"], "receipt_updated": False}
        try:
            receipt["status"] = "committed"
            save_receipt(receipt_path, receipt)
            result["receipt_updated"] = True
        except Exception:
            pass
        return result
    except BaseException:
        if committed:
            return result
        if db.in_transaction:
            db.rollback()
        raise
    finally:
        db.close()

if __name__ == "__main__":
    try:
        database = Path(sys.argv[1]).resolve(strict=True)
        router_id = int(sys.argv[2])
        payload = json.loads(sys.stdin.read() or '{}')
        if payload.get("mode", "preview") == "preview":
            result = plan(database, router_id)[0]
        elif payload.get("mode") == "execute":
            result = execute(database, router_id, payload)
        else:
            raise RuntimeError("Mode tidak valid.")
        print(json.dumps(result, separators=(",", ":")))
    except BaseException as error:
        print(type(error).__name__ + ": " + str(error), file=sys.stderr)
        sys.exit(1)
